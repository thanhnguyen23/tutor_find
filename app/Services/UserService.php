<?php

namespace App\Services;

use App\Repositories\Eloquent\UserRepository;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ProfileCompletionReminder;
use App\Mail\WelcomeNewTutor;
use App\Models\NotificationType;
use Carbon\Carbon;

class UserService
{
    protected $userRepository;
    const MAX_REMINDER_EMAILS = 3;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Send welcome email to new tutor with profile completion reminder
     */
    public function sendWelcomeEmail($user)
    {
        try {
            Mail::to($user->email)->send(new WelcomeNewTutor($user));
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send profile completion reminder email
     */
    public function sendProfileCompletionReminder($user)
    {
        try {
            if ($user->reminder_emails_sent >= self::MAX_REMINDER_EMAILS) {
                return false;
            }

            // Check if it's been at least a week since the last reminder
            $lastReminder = $user->last_reminder_sent_at ? Carbon::parse($user->last_reminder_sent_at) : null;
            if ($lastReminder && $lastReminder->addWeek()->isFuture()) {
                return false;
            }

            Mail::to($user->email)->send(new ProfileCompletionReminder($user));

            // Update reminder count and timestamp
            $user->update([
                'reminder_emails_sent' => $user->reminder_emails_sent + 1,
                'last_reminder_sent_at' => now()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send reminder email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Check if user needs a reminder email
     */
    public function needsProfileCompletionReminder($user)
    {
        if ($user->profile_completed || $user->reminder_emails_sent >= self::MAX_REMINDER_EMAILS) {
            return false;
        }

        // If no reminder has been sent yet
        if (!$user->last_reminder_sent_at) {
            return true;
        }

        // Check if it's been a week since the last reminder
        $lastReminder = Carbon::parse($user->last_reminder_sent_at);
        return $lastReminder->addWeek()->isPast();
    }

    /**
     * Calculate profile completion percentage and details
     *
     * @param mixed $user User data with relationships
     * @return array Profile completion data
     */
    public function calculateProfileCompletion($user)
    {
        $completionDetails = [
            'personal_info' => false, // Thông tin cá nhân
            'education' => false,     // Học vấn
            'experience' => false,    // Kinh nghiệm
            'subjects' => false,      // Môn học giảng dạy
            'languages' => false,     // Ngôn ngữ
            'schedule' => false,      // Lịch trình hàng tuần
            'study_form' => false     // Hình thức học
        ];

        // Check personal info completion (Thông tin cá nhân)
        $completionDetails['personal_info'] =
            !empty($user->first_name) &&
            !empty($user->last_name) &&
            !empty($user->sex) &&
            !empty($user->phone) &&
            !empty($user->email) &&
            !empty($user->provinces_id) &&
            !empty($user->districts_id) &&
            !empty($user->wards_id) &&
            !empty($user->address) &&
            !empty($user->about_you) &&
            !empty($user->cccd) &&
            !empty($user->cccd_front) &&
            !empty($user->cccd_back);

        // Check education records (Học vấn ít nhất 1)
        $completionDetails['education'] = $user->userEducations->count() > 0;

        // Check experience records (Kinh nghiệm ít nhất 1)
        $completionDetails['experience'] = $user->userExperiences->count() > 0;

        // Check subjects (Môn học giảng dạy ít nhất 1)
        $completionDetails['subjects'] = $user->userSubjects->count() > 0;

        // Check languages (Ngôn ngữ ít nhất 1)
        $completionDetails['languages'] = $user->userLanguages->count() > 0;

        // Check weekly schedule (Lịch trình hàng tuần)
        $completionDetails['schedule'] = $user->userWeeklyTimeSlots->count() > 0;

        // Check study locations (Hình thức học)
        $completionDetails['study_form'] = $user->userStudyLocations->count() > 0;

        // Calculate percentage
        $totalFields = count($completionDetails);
        $completedFields = count(array_filter($completionDetails));
        $percent = round(($completedFields / $totalFields) * 100);

        // Determine if fully completed
        $completed = $completedFields === $totalFields;

        return [
            'percent' => $percent,
            'completed' => $completed,
            'details' => $completionDetails
        ];
    }

    public function updateAndCheckProfileCompletion($userData, array $data)
    {
        if (!empty($data)) {
            $userData->update($data);
        }

        $completionData = $this->calculateProfileCompletion($userData);
        if ($completionData['completed'] === true) {
            $userData->markProfileCompleted();
        }
        return $userData;
    }

    /**
     * Tính toán và lưu trạng thái hoàn thành hồ sơ cho user (dùng cho observer)
     */
    public function calculateAndSaveProfileCompletion($user)
    {
        // Eager load các quan hệ nếu chưa có
        $user->loadMissing([
            'userSubjects',
            'userEducations',
            'userExperiences',
            'userWeeklyTimeSlots',
            'userLanguages',
            'userStudyLocations',
        ]);
        $wasCompleted = (bool)$user->profile_completed;
        $completionData = $this->calculateProfileCompletion($user);
        $type_profile = NotificationType::$TYPE_PROFILE;

        if ($completionData['completed'] === true) {
            $user->markProfileCompleted();
            if (!$wasCompleted) {
                // Gửi notification khi hoàn thành profile lần đầu
                app(\App\Repositories\Contracts\NotificationLogRepositoryInterface::class)->create([
                    'uid' => $user->uid,
                    'name' => 'Hoàn thành hồ sơ',
                    'description' => 'Bạn đã hoàn thành hồ sơ. Vui lòng chờ xác thực các thông tin chính xác.',
                    'notification_type' => $type_profile,
                ]);
            }
        } else {
            $user->markProfileNotCompleted();
        }
    }
}
