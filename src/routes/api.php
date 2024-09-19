<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\RegistrantController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\FeeController;

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
Route::apiResource('students', StudentController::class);
Route::apiResource('registrants', RegistrantController::class);
Route::apiResource('teachers', TeacherController::class);
Route::apiResource('groups', GroupController::class);
Route::apiResource('sections', SectionController::class);
Route::apiResource('payments', PaymentController::class);
Route::apiResource('fees', FeeController::class);