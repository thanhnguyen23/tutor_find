<?php

namespace App\Repositories\Eloquent;

use App\Models\Conversation;
use App\Repositories\Contracts\ConversationRepositoryInterface;

class ConversationRepository implements ConversationRepositoryInterface
{
    // Triển khai các hàm của Interface
    protected $model;

    public function __construct(Conversation $model)
    {
        $this->model = $model;
    }

    public function create($data) {
        return $this->model->create($data);
    }

    public function find($userId, $receiverId) {
        return $this->model
        ->with(['user1', 'user2', 'messages', 'lastMessage'])
        ->where(function ($query) use ($userId, $receiverId) {
            $query
            ->where('user1_uid', $userId)
            ->where('user2_uid', $receiverId);
        })->orWhere(function ($query) use ($userId, $receiverId) {
            $query
            ->where('user1_uid', $receiverId)
            ->where('user2_uid', $userId);
        })->first();
    }
    public function getContactedUsers($userId) {
        return $this->model
        ->where('user1_uid', $userId)
        ->orWhere('user2_uid', $userId)
        ->with(['user1', 'user2', 'lastMessage'])
        ->get();
    }
}
