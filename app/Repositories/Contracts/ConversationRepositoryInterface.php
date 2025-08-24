<?php

namespace App\Repositories\Contracts;

interface ConversationRepositoryInterface
{
    // Định nghĩa các hàm cần thiết
    public function create($data);

    public function find($userId, $receiverId);

    public function getContactedUsers($userId);
}
