<?php

use App\Http\Controllers\Admin\ConfigurationController;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TutorAdsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NotificationLogController;
use App\Http\Controllers\Api\UserBookingController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\ChatController;
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
        Route::post('/profile-info', 'updateProfileInfo');
        Route::put('/experience', 'updateExperience');
        Route::put('/packages/{id}', 'updateUserPackage');
        Route::put('/subject', 'updateSubject');
        Route::post('/subject', 'addSubject');
        Route::post('/update-education', 'updateEducation');
        Route::post('/education', 'addEducation');
        Route::post('/experience', 'addExperience');
        Route::post('/packages', 'createUserPackage');
        Route::delete('/packages/{id}', 'deleteUserPackage');
        Route::delete('/education/{id}', 'deleteEducation');
        Route::delete('/experience/{id}', 'deleteExperience');
        Route::delete('/subject/{id}', 'deleteSubject');
        Route::get('/schedule', 'getWeeklySchedule');
        Route::put('/schedule/{id}', 'updateTimeSlot');
        Route::post('/schedule', 'updateWeeklySchedule');
        Route::delete('/schedule/{id}', 'deleteTimeSlot');
        Route::get('/languages', 'getUserLanguages');
        Route::post('/languages', 'addLanguage');
        Route::put('/languages/{id}', 'updateLanguage');
        Route::delete('/languages/{id}', 'deleteLanguage');
        Route::post('/studyLocation', 'addStudyLocation');
        Route::get('/profile-completion', 'getProfileCompletion');
    });

    Route::prefix('bookings')->controller(UserBookingController::class)->group(function () {
        Route::get('/', 'getAll');
        Route::post('/', 'store');
        Route::get('/coming-lessons', 'getUpcomingLessons');
        Route::get('/{id}', 'show');
        Route::post('/change-status', 'changeStatus');
    });

    Route::prefix('notifications')->controller(NotificationLogController::class)->group(function () {
        Route::get('/', 'getAll');
        Route::post('/mark-all-read', 'markAllAsRead');
        Route::post('/{id}/mark-read', 'markAsRead');
    });

    Route::post('/send-message', [ChatController::class, 'sendMessage']);
    Route::post('/conversations', [ChatController::class, 'createConversation']);
    Route::get('/messages/{receiverId}', [ChatController::class, 'getMessages']);
    Route::get('/contacted-users', [ChatController::class, 'getContactedUsers']);
});

Route::get('/tutors', [UserController::class, 'getTutors']);
Route::get('/tutor/{uid}', [UserController::class, 'getTutorDetail']);

// Test broadcasting route
Route::post('/test-broadcast', function () {
    $message = new \App\Models\Message();
    $message->id = 1;
    $message->receiver_id = 1;
    $message->content = 'Test message';

    broadcast(new \App\Events\MessageSent($message));

    return response()->json(['message' => 'Event broadcasted']);
});
