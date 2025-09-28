<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{receiverId}', function ($user, $receiverId) {
    return (int) $user->uid === (int) $receiverId;
});

Broadcast::channel('chat.presence', function ($user) {
    return ['id' => $user->uid, 'name' => $user->full_name, 'avatar' => $user->avatar];
});

// Presence channel for WebRTC classroom rooms
Broadcast::channel('classroom.{classroomId}', function ($user, string $classroomId) {
    // Allow authenticated users to join. You can add ACL: only tutor/student of this classroom.
    return [
        'uid' => $user->uid,
        'name' => $user->full_name,
        'avatar' => $user->avatar,
        'classroomId' => $classroomId,
    ];
});

Broadcast::channel('private-webrtc.{to}', function ($user, $to) {
    return $user->uid === $to; // Chỉ cho phép user đích nhận
});

// Private payment updates channel per user
Broadcast::channel('payments.{uid}', function ($user, $uid) {
    return (string) $user->uid === (string) $uid;
});
