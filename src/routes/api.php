<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;

Route::get('user', [AuthController::class, "getUser"]);
Route::post('register',[AuthController::class,'register']);
Route::post('login', [AuthController::class,'login']);
Route::post('refresh', [AuthController::class,'refresh']);
Route::post('logout', [AuthController::class,'logout']);


Route::apiResource('users', UserController::class);
Route::put('users/{id}/toggle', [UserController::class, 'toggle']);
Route::post('user/{id}/update', [ProfileController::class, 'editInfos']);
Route::post('user/{id}/profile', [ProfileController::class, 'editProfilePicture']);
Route::post('user/{id}/password', [ProfileController::class, 'changePassword']);