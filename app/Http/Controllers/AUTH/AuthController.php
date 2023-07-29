<?php

namespace App\Http\Controllers\AUTH;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordMail;
use App\Mail\VerificationMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{

    public function getAllData()
    {
        $data = User::with('divisi')->get();
        return response()->json([
            'message' => 'success get all data',
            'data' => $data
        ]);
    }

    public function register(Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|unique:users',
                'id_divisi' => 'required',
                'password' => 'required|confirmed',
                'password_confirmation' => 'required'
            ],
            [
                'name.required' => 'Form name tidak boleh kosong',
                'email.required' => 'Form email tidak boleh kosong',
                'id_divisi.required' => 'Form divisi tidak boleh kosong',
                'email.unique' => 'Email sudah pernah terdaftar sebelumnya',
                'password.required' => 'Form password tidak boleh kosong',
                'password.confirmed' => 'Password harus sama',
                'password_confirmation.required' => 'Form password confirmasi tidak boleh kosong'
            ]
        );

        if ($validation->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'check your validation',
                'errors' => $validation->errors()
            ]);
        }
        try {
            $data = new User;
            $data->uuid = Uuid::uuid4()->toString();
            $data->name = $request->input('name');
            $data->id_divisi = $request->input('id_divisi');
            $data->role = $request->input('role', 2);
            $data->email = $request->input('email');
            $data->password = Hash::make($request->input('password'));
            $data->save();

            $this->sendVerificationEmail($data);

            $token = $data->createToken('auth_token')->plainTextToken;
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 400,
                'message' => 'failed register',
                'errors' => $th->getMessage()
            ]);
        }
        return response()->json([
            'code' => 200,
            'message' => 'success register',
            'data' => $data,
            'access_token' => $token
        ]);
    }


    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'code' => 400,
                'message' => 'Invalid email or password'
            ]);
        }

        $user = User::where('email', $request['email'])->first();

        if (!$user || !$user->email_verified_at) {
            Auth::logout();
            return response()->json([
                'message' => 'Email not verified',
                'code' => 422
            ]);
        }
        $token = $user->createToken('auth_token');

        if (!$this->isTokenValid($token)) {
            Auth::logout();
            return response()->json([
                'code' => 400,
                'message' => 'Token expired'
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'success login',
            'data' => $user,
            'access_token' => $token->plainTextToken
        ]);
    }

    private function isTokenValid($token)
    {
        $expirationMinutes = config('sanctum.expiration');

        if ($expirationMinutes === null) {
            return true; // Token tidak kedaluwarsa jika tidak ada batasan waktu
        }
        // Periksa apakah waktu pembuatan token ditambah dengan waktu kedaluwarsa masih lebih besar dari waktu saat ini
        return $token->accessToken->created_at->addMinutes($expirationMinutes)->isFuture();
    }



    public function verifyEmail(Request $request)
    {
        $user = User::where('email', $request->email)->firstOrFail();
        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Email already verified',
                'code' => 400
            ]);
        }

        $user->email_verified_at = now();
        $user->save();
        return redirect('/login')->with([
            'success' => 'Email verified successfully',
            'data' => $user,
            'code' => 200
        ]);
    }


    private function sendVerificationEmail(User $user)
    {
        $verificationUrl = url('v3/396d6585-16ae-4d04-9549-c499e52b75ea/auth/verify-email/' . $user->email);
        Mail::to($user->email)->send(new VerificationMail($verificationUrl));
        return response()->json([
            'message' => 'Success sending verification email',
            'code' => 200
        ]);
    }


    //reset password

    public function forgotPassword(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);
        if ($validation->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'check your validation',
                'errors' => $validation->errors()
            ]);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'code' => 400,
                'message' => 'Email not found'
            ]);
        }
        $token = Uuid::uuid4()->toString();
        $user->reset_password_token = $token;
        $user->save();
        $this->sendVerificationPassword($user);
        return response()->json([
            'code' => 200,
            'message' => 'Link reset password sudah terkirim, silahkan check email anda'
        ]);
    }

    private function sendVerificationPassword(User $user)
    {
        $verificationUrl = url('v3/view-reset/' . $user->reset_password_token);
        Mail::to($user->email)->send(new ForgotPasswordMail($verificationUrl));

        return response()->json([
            'message' => 'Success sending verification email',
            'code' => 200
        ]);
    }

    public function verifyPassword(Request $request)
    {
        $user = User::where('reset_password_token', $request->reset_password_token)->firstOrFail();
        return redirect()->to('/reset-password/' . $request->reset_password_token)->with([
            'success' => 'Email verified successfully',
            'data' => $user,
            'code' => 200
        ]);
    }

    public function resetPassword(Request $request, $reset_password_token)
    {
        $user = User::where('reset_password_token', $reset_password_token)->first();

        if (!$user) {
            return response()->json([
                'code' => 400,
                'message' => 'Token invalid'
            ]);
        }

        $validation = Validator::make($request->all(), [
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);

        if ($validation->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'check your validation',
                'errors' => $validation->errors()
            ]);
        }

        try {
            $user->password = Hash::make($request->input('password'));
            $user->reset_password_token = null;
            $user->save();
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 400,
                'message' => 'failed',
                'errors' => $th->getMessage()
            ]);
        }


        return response()->json([
            'code' => 200,
            'message' => 'success reset password'
        ]);
    }

    public function changePassword(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);

        if ($validation->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Check your validation',
                'errors' => $validation->errors()
            ]);
        }

        try {
            $user = User::find(Auth::id());
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'code' => 422,
                    'message' => 'Incorrect old password'
                ]);
            }

            $user->password = Hash::make($request->input('password'));
            $user->save();

            return response()->json([
                'code' => 200,
                'message' => 'Password changed successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 400,
                'message' => 'Failed',
                'errors' => $th->getMessage()
            ]);
        }
    }


    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'code' => 200,
            'message' => 'sucess logout and delete token access'
        ]);
    }
}
