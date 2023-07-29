<?php


use App\Http\Controllers\API\AbsenController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\DivisiController;
use App\Http\Controllers\AUTH\AuthController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/login', function () {
    return view('auth.login');
});
Route::get('/register', function () {
    return view('auth.register');
});

Route::get('/forgot', function () {
    return view('auth.forgot-password');
});
Route::post('/forgot', function () {
    return view('auth.forgot-password');
});

Route::get('/reset-password/{password_reset_token}', function () {
    return view('auth.reset_password');
});

//auth
Route::prefix('v3')->controller(AuthController::class)->group(function () {
    Route::get('/396d6585-16ae-4d04-9549-c499e52b75ea/auth/verify-email/{email}', 'verifyEmail');
    Route::post('/396d6585-16ae-4d04-9549-c499e52b75ea/auth/register', 'register');
    Route::post('/396d6585-16ae-4d04-9549-c499e52b75ea/auth/login', 'login');
    Route::get('/396d6585-16ae-4d04-9549-c499e52b75ea/auth', 'getAllData');
    Route::post('396d6585-16ae-4d04-9549-c499e52b75ea/auth/forgot-password/', 'forgotPassword');
    Route::post('/396d6585-16ae-4d04-9549-c499e52b75ea/auth/reset-password/{password_reset_token}', 'resetPassword');
    Route::get('/view-reset/{reset_password_token}', 'verifyPassword');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/count', [DashboardController::class, 'countData']);
    Route::get('/', function () {
        return view('user.dashboard');
    });

    Route::get('/divisi', function () {
        return view('user.divisi');
    })->middleware('role:1');

    Route::get('/data/absen', function () {
        return view('user.data-absen');
    })->middleware('role:1');;

    Route::post('/data/absen', function () {
        return view('user.data-absen');
    })->middleware('role:1');;

    Route::get('/absen', function () {
        return view('user.absen');
    })->middleware('role:2');;

    Route::post('/logout', [AuthController::class, 'logout']);

    //absen
    Route::prefix('v2')->controller(AbsenController::class)->group(function () {
        Route::get('/febba411-89e8-4fb3-9f55-85c56dcff41d/absen', 'getAllData');
        Route::post('/396d6585-16ae-4d04-9549-c499e52b75ea/absen/create', 'createAbsen');
        Route::get('/9d97457b-1922-4f4a-b3fa-fcba980633a2/absen/get/{uuid}', 'getDataByUuid');
        Route::put('/4a3f479a-eb2e-498f-aa7b-e7d6e3f0c5f3/absen/update/{uuid}', 'updateData');
        Route::delete('/83df59b0-7c1a-4944-8fbb-2c06670dfa01/absen/delete/{uuid}', 'deleteData');
        Route::get('/febba411-89e8-4fb3-9f55-85c56dcff41d/absen/user', 'getDataUser');
        Route::post('/396d6585-16ae-4d04-9549-c499e52b75ea/admin/control', 'adminControl');
    });
});
//API DIVISI
Route::prefix('v1')->controller(DivisiController::class)->group(function () {
    Route::get('/febba411-89e8-4fb3-9f55-85c56dcff41d/divisi', 'getAllData');
    Route::post('/396d6585-16ae-4d04-9549-c499e52b75ea/divisi/create', 'createData');
    Route::get('/9d97457b-1922-4f4a-b3fa-fcba980633a2/divisi/get/{uuid}', 'getDataByUuid');
    Route::put('/4a3f479a-eb2e-498f-aa7b-e7d6e3f0c5f3/divisi/update/{uuid}', 'updateData');
    Route::delete('/83df59b0-7c1a-4944-8fbb-2c06670dfa01/divisi/delete/{uuid}', 'deleteData');
});
