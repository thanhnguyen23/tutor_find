<?php

use App\Http\Controllers\Admin\ConfigurationController;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TutorAdsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClassRoomController;
use App\Http\Controllers\Api\ClassRoomCustomController;
use App\Http\Controllers\Api\NotificationLogController;
use App\Http\Controllers\Api\UserBookingController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\VnPayController;
use App\Http\Controllers\Api\UserBookingComplaintController;
use App\Http\Controllers\Api\ZoomController;
use App\Http\Controllers\MeetingController;
use Illuminate\Support\Facades\Log;

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

    Route::prefix('me')->controller(UserController::class)->group(function () {
        Route::get('/', 'me');
    });

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
        Route::get('/available-for-classroom', 'getAvailableForClassroom');
        Route::get('/', 'getAll');
        Route::post('/', 'store');
        Route::post('/sepay', 'storeSepay');
        Route::post('/trial', 'storeTrial');
        Route::get('/coming-lessons', 'getUpcomingLessons');
        Route::get('/{id}', 'show');
        Route::post('/change-status', 'changeStatus');
    });
    Route::prefix('bookings')->group(function () {
        Route::post('/complaints', [UserBookingComplaintController::class, 'store']);
        Route::post('/complaints/update-status', [UserBookingComplaintController::class, 'updateStatus']);
        Route::get('/{bookingId}/complaint', [UserBookingComplaintController::class, 'showByBooking']);
    });

    Route::prefix('notifications')->controller(NotificationLogController::class)->group(function () {
        Route::get('/', 'getAll');
        Route::post('/mark-all-read', 'markAllAsRead');
        Route::post('/{id}/mark-read', 'markAsRead');
    });

    Route::controller(ChatController::class)->group(function () {
        Route::post('/send-message', 'sendMessage');
        Route::post('/conversations', 'createConversation');
        Route::get('/messages/{receiverId}', 'getMessages');
        Route::get('/contacted-users', 'getContactedUsers');
    });

    // VNPAY payment routes
    Route::prefix('vnpay')->controller(VnPayController::class)->group(function () {
        Route::post('/create-payment', 'createPayment');
        Route::post('/ipn', 'ipn');
    });

    Route::prefix('classrooms')->controller(ClassRoomController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
        Route::post('/', 'store');
        Route::post('/{id}/start', 'start');
        Route::post('/{id}/end', 'end');
        Route::post('/{id}/retry', 'retry');
        Route::post('/{id}/join', 'join');
        Route::post('pusher-webhook', 'pusherWebhook');
    });

    // WebRTC signaling endpoints (Laravel WebSockets)
    Route::prefix('webrtc')->controller(ClassRoomCustomController::class)->group(function () {
        Route::post('/join', 'join');
        Route::post('/signal', 'signal');
    });
});

Route::post('/classrooms/pusher-webhook', [ClassRoomController::class, 'pusherWebhook']);
Route::post('/bookings-payment/webhook', [UserBookingController::class, 'webhookSendPayment']);

Route::get('/tutors', [UserController::class, 'search']);
Route::get('/tutor/{uid}', [UserController::class, 'getTutor']);

Route::get('/vnpay-s/return', [VnPayController::class, 'return']);
