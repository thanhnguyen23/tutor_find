<?php

use App\Http\Controllers\Admin\ConfigurationController;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TutorAdsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Http\JsonResponse;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(ConfigurationController::class)->group(function () {
    Route::get('/configurations', 'getAllConfigurations');
});

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/verify-token', [AuthController::class, 'verifyToken']);

    Route::prefix('me')->controller(UserProfileController::class)->group(function () {
        Route::get('/profile', 'getUserDataDetail');
        Route::put('/profile', 'updateUserData');
        Route::put('/education', 'updateEducation');
        Route::put('/experience', 'updateExperience');
        Route::put('/subject', 'updateSubject');
    });
});
