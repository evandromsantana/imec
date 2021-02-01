<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MechanicController;


Route::get('/ping', function() {
    return ['pong'=>true];
});

Route::get('401', [AuthController::class, 'unauthorizad'])->name('login');

//Route::get('/radom', [MechanicController::class, 'createRandom']);

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout']);
Route::post('/auth/refresh', [AuthController::class, 'refresh']);
Route::post('/user', [AuthController::class, 'create']);

Route::get('/user', [UserController::class, 'read']);
Route::put('/user', [UserController::class, 'update']);
Route::post('/user/avatar', [UserController::class, 'updateAvatar']);
Route::get('user/favorites', [UserController::class, 'getFavorites']);
Route::post('/user/favorite', [UserController::class, 'toggleFavorite']);
Route::get('/user/appointments', [UserController::class, 'getAppointments']);

Route::get('/mechanics', [MechanicController::class, 'list']);
Route::get('/mechanic/{id}', [MechanicController::class, 'one']);
Route::post('/mechanic/{id}/appointment', [MechanicController::class, 'setAppointment']);

Route::get('/search', [MechanicController::class, 'search']);