<?php

use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Clinic\ClinicController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function (Request $request) {
    return response([
        'message' => 'api is available'
    ], 200);
});

Route::middleware(['auth:sanctum'])->group(function () {
    //route auth
    Route::get('/logout', [AuthenticationController::class, 'logout']);
    Route::patch('/user/update/{id}', [AuthenticationController::class, 'update']);
    Route::delete('/user/delete/{id}', [AuthenticationController::class, 'destroy']);

    //route clinic-history
    Route::get('/clinic/data/admin', [ClinicController::class, 'showDataAdmin']);
    Route::get('/clinic/data/user', [ClinicController::class, 'showDataUser']);
    Route::get('/clinic/data/dokter', [ClinicController::class, 'showDataDoctor']);
    Route::post('/clinic', [ClinicController::class, 'store']);
    Route::patch('/clinic/update/{id}', [ClinicController::class, 'update']);
    Route::delete('/clinic/delete/{id}', [ClinicController::class, 'destroy']);
});

//route auth
// Route::get('/user/data', [AuthenticationController::class, 'index']);

Route::get('/user/data', [AuthenticationController::class, 'index']);
Route::get('/user/data/{id}', [AuthenticationController::class, 'byId']);
Route::post('/register', [AuthenticationController::class, 'register']);
Route::post('/login', [AuthenticationController::class, 'login']);