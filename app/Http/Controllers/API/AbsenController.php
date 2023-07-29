<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AbsenModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class AbsenController extends Controller
{
    public function getAllData()
    {
        $data = AbsenModel::with(['users' => function ($query) {
            $query->select('id', 'uuid', 'name', 'email', 'id_divisi');
        }, 'users.divisi:id,nama_divisi'])->get();

        if ($data->isEmpty()) {
            return response()->json([
                'code' => 404,
                'message' => 'Data not found',
            ]);
        } else {
            return response()->json([
                'code' => 200,
                'message' => 'success get all data',
                'data' => $data,
            ]);
        }
    }

    public function getDataUser()
    {
        $user = Auth::user();
        $data = AbsenModel::with(['users' => function ($query) {
            $query->select('id', 'uuid', 'name', 'email', 'id_divisi');
        }, 'users.divisi:id,nama_divisi'])->where('id_user', $user->id)->get();

        if ($data->isEmpty()) {
            return response()->json([
                'code' => 404,
                'message' => 'Data not found',
            ]);
        } else {
            return response()->json([
                'code' => 200,
                'message' => 'success get all data',
                'data' => $data,
            ]);
        }
    }

    public function createAbsen()
    {
        $tanggalSekarang = now()->toDateString();
        $absensiHariIni = AbsenModel::where('tanggal', $tanggalSekarang)->first();

        if ($absensiHariIni) {
            return response()->json([
                'code' => 422,
                'message' => 'Anda sudah melakukan absen hari ini.'
            ]);
        }
        $jamAbsen = now()->toTimeString();
        if (strtotime($jamAbsen) > strtotime('12:00:00')) {
            $status = 'tidak hadir';
        } elseif (strtotime($jamAbsen) > strtotime('08:00:00')) {
            $status = 'lambat';
        } else {
            $status = 'hadir';
        }

        try {
            $user = Auth::user();
            $absensi = new AbsenModel;
            $absensi->uuid = Uuid::uuid4()->toString();
            $absensi->id_user = $user->id;
            $absensi->tanggal = $tanggalSekarang;
            $absensi->waktu = now()->toTimeString();
            $absensi->status = $status;
            $absensi->save();
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 400,
                'message' => 'failed',
                'errors' => $th->getMessage()
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Absen berhasil disimpan.',
            'data' => $absensi
        ]);
    }


    public function adminControl()
    {
        $jamSekarang = now()->toTimeString();
        if (strtotime($jamSekarang) > strtotime('08:00:00')) {
            $users = User::where('role', 2)->get();
            $isAllUsersAbsen = true;

            foreach ($users as $user) {
                $absen = AbsenModel::where('id_user', $user->id)
                    ->whereDate('tanggal', Carbon::today()->toDateString())
                    ->first();

                if (!$absen) {
                    $status = 'tidak hadir'; 

                    $absensi = new AbsenModel;
                    $absensi->uuid = Uuid::uuid4()->toString();
                    $absensi->id_user = $user->id;
                    $absensi->tanggal = now()->toDateString();
                    $absensi->waktu = now()->toTimeString();
                    $absensi->status = $status;
                    $absensi->save();

                    $isAllUsersAbsen = false;
                }
            }

            if ($isAllUsersAbsen) {
                return response()->json([
                    'code' => 400,
                    'message' => 'Semua pengguna sudah melakukan absensi hari ini. Fungsi adminControl tidak dapat dijalankan.'
                ]);
            } else {
                return response()->json([
                    'code' => 200,
                    'message' => 'Fungsi adminControl berhasil dijalankan.',
                ]);
            }
        } else {
            return response()->json([
                'code' => 401,
                'message' => 'Fungsi adminControl hanya dapat dijalankan setelah jam 12 siang.'
            ]);
        }
    }
}
