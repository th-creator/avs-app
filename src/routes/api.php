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
use App\Http\Controllers\ExpanseController;
use App\Http\Controllers\TeacherExpanseController;

Route::get('user', [AuthController::class, "getUser"]);
Route::post('register',[AuthController::class,'register']);
Route::post('login', [AuthController::class,'login']);
Route::post('refresh', [AuthController::class,'refresh']);
Route::post('logout', [AuthController::class,'logout']);


Route::apiResource('teacherExpanses', TeacherExpanseController::class);
Route::apiResource('users', UserController::class);
Route::put('users/{id}/toggle', [UserController::class, 'toggle']);
Route::post('user/{id}/update', [ProfileController::class, 'editInfos']);
Route::post('user/{id}/profile', [ProfileController::class, 'editProfilePicture']);
Route::post('user/{id}/password', [ProfileController::class, 'changePassword']);
Route::apiResource('students', StudentController::class);
Route::apiResource('registrants', RegistrantController::class);
Route::apiResource('expanses', ExpanseController::class);
Route::put('registrant/{id}/toggle', [RegistrantController::class, 'toggle']);
Route::post('registrant/{id}/transfer', [RegistrantController::class, 'transfer']);
Route::get('group/{id}/registrants', [RegistrantController::class, 'groupRegistrants']);
Route::get('group/{id}/payments', [PaymentController::class, 'groupPayments']);
Route::apiResource('teachers', TeacherController::class);
Route::apiResource('groups', GroupController::class);
Route::get('group/Payments/{month}', [GroupController::class,'allPayments']);
Route::get('student/{id}/groups', [GroupController::class,'studentGroups']);
Route::apiResource('sections', SectionController::class);
Route::apiResource('payments', PaymentController::class);
Route::get('all/paymens', [PaymentController::class, 'all']);
Route::apiResource('fees', FeeController::class);
Route::get('undandledFees', [FeeController::class, 'undandledFees']);
Route::post('finance/payments', [PaymentController::class, 'fetchFinance']);
Route::post('finance/fees', [FeeController::class, 'fetchFinance']);
Route::post('finance/expanses', [ExpanseController::class, 'fetchFinance']);
Route::post('finance/teacherExpanses', [TeacherExpanseController::class, 'fetchFinance']);
Route::get('doubled/registrants', [RegistrantController::class, 'doubled']);