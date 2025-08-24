<?php

namespace App\Repositories\Eloquent;

use App\Models\Message;
use App\Repositories\Contracts\MessageRepositoryInterface;

class MessageRepository implements MessageRepositoryInterface
{
    // Triển khai các hàm của Interface
    protected $model;

    public function __construct(Message $model)
    {
        $this->model = $model;
    }

    public function create($data) {
        return $this->model->create($data);
    }

    public function getMessages($conversation_id) {
        return $this
        ->model
        ->with(['receiver', 'sender'])
        ->where('conversation_id', $conversation_id)
        ->get();
    }
}
