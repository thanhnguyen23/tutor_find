<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $other_user = $this->user1_uid === auth()->user()->uid ? $this->user2 : $this->user1;

        return [
            "id" => $this->id,
            "user1_uid" => $this->user1_uid,
            "user2_uid" => $this->user2_uid,
            "last_message_id" => $this->last_message_id,
            "last_message" => $this->lastMessage,
            "other_user" => [
                "id" => $other_user->id,
                "uid" => $other_user->uid,
                "full_name" => $other_user->full_name,
                "avatar" => $other_user->avatar,
            ],
            "last_message" => [
                "id" => $this->lastMessage->id,
                "conversation_id" => $this->lastMessage->conversation_id,
                "sender_id" => $this->lastMessage->sender_id,
                "receiver_id" => $this->lastMessage->receiver_id,
                "content" => $this->lastMessage->content,
                "is_read" => $this->lastMessage->is_read,
                "created_at" => $this->lastMessage->created_at,
                "updated_at" => $this->lastMessage->updated_at,
            ],
            'messages' => $this->messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'content' => $message->content,
                    'created_at' => $message->created_at,
                    'is_sent' => $message->sender_id == auth()->user()->uid,
                    'is_read' => $message->is_read,
                ];
            }),
            'is_online' => $this->is_online,
        ];
    }
}
