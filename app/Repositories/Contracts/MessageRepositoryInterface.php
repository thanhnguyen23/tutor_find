<?php

namespace App\Repositories\Contracts;

interface MessageRepositoryInterface
{
    // Định nghĩa các hàm cần thiết
    public function create($data);

    public function getMessages($conversation_id);
}
