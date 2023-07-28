<?php

use App\Http\Controllers\API\AbsenController;
use App\Http\Controllers\API\DivisiController;
use App\Http\Controllers\AUTH\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('layouts.base');
});

Route::get('/divisi', function () {
    return view('user.divisi');
});

Route::get('/absen', function () {
    return view('user.absen');
});

Route::get('/waktu', [DivisiController::class, 'testTimezone']);


//API DIVISI
Route::prefix('v1')->controller(DivisiController::class)->group(function () {
    Route::get('/febba411-89e8-4fb3-9f55-85c56dcff41d/divisi', 'getAllData');
    Route::post('/396d6585-16ae-4d04-9549-c499e52b75ea/divisi/create', 'createData');
    Route::get('/9d97457b-1922-4f4a-b3fa-fcba980633a2/divisi/get/{uuid}', 'getDataByUuid');
    Route::put('/4a3f479a-eb2e-498f-aa7b-e7d6e3f0c5f3/divisi/update/{uuid}', 'updateData');
    Route::delete('/83df59b0-7c1a-4944-8fbb-2c06670dfa01/divisi/delete/{uuid}', 'deleteData');
});


//auth
Route::prefix('v3')->controller(AuthController::class)->group(function () {
    Route::get('/396d6585-16ae-4d04-9549-c499e52b75ea/auth/verify-email/{email}', 'verifyEmail');
    Route::post('/396d6585-16ae-4d04-9549-c499e52b75ea/auth/register', 'register');
    Route::post('/396d6585-16ae-4d04-9549-c499e52b75ea/auth/login', 'login');
    Route::post('/396d6585-16ae-4d04-9549-c499e52b75ea/auth/logout', 'logout');
    Route::get('/396d6585-16ae-4d04-9549-c499e52b75ea/auth', 'getAllData');
});

Route::middleware('web', 'auth')->group(function () {
    //absen
    Route::prefix('v2')->controller(AbsenController::class)->group(function () {
        Route::get('/febba411-89e8-4fb3-9f55-85c56dcff41d/absen', 'getAllData');
        Route::post('/396d6585-16ae-4d04-9549-c499e52b75ea/absen/create', 'createAbsen');
        Route::get('/9d97457b-1922-4f4a-b3fa-fcba980633a2/absen/get/{uuid}', 'getDataByUuid');
        Route::put('/4a3f479a-eb2e-498f-aa7b-e7d6e3f0c5f3/absen/update/{uuid}', 'updateData');
        Route::delete('/83df59b0-7c1a-4944-8fbb-2c06670dfa01/absen/delete/{uuid}', 'deleteData');
    });
});
