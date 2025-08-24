<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Resources\ChatResource;
use App\Models\Message;
use App\Repositories\Contracts\ConversationRepositoryInterface;
use App\Repositories\Contracts\MessageRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Redis;

class ChatController extends Controller
{
    protected $messageRepositoryInterface;
    protected $conversationRepositoryInterface;

    public function __construct(MessageRepositoryInterface $messageRepositoryInterface, ConversationRepositoryInterface $conversationRepositoryInterface)
    {
        $this->messageRepositoryInterface = $messageRepositoryInterface;
        $this->conversationRepositoryInterface = $conversationRepositoryInterface;
    }

    public function sendMessage(Request $request)
    {
        $userId = auth()->user()->uid;
        $receiverId = $request->receiver_id;

        $conversation = $this->conversationRepositoryInterface->find($userId, $receiverId);

        if (!$conversation) {
            $conversation =  $this->conversationRepositoryInterface->create([
                'user1_uid' => min($userId, $receiverId), // Đảm bảo user1_uid < user2_uid để tránh trùng lặp
                'user2_uid' => max($userId, $receiverId),
            ]);
        }

        $message = $this->messageRepositoryInterface->create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->user()->uid,
            'receiver_id' => $request->receiver_id,
            'content' => $request->content,
        ]);

        $conversation->update(['last_message_id' => $message->id]);
        $conversation->refresh(); // Lấy lại dữ liệu mới nhất

        $payload = [
            'message' => $message,
            'last_message' => $conversation->lastMessage, // Giả sử có quan hệ last_message
        ];
        broadcast(new MessageSent($payload));

        return response()->json(['message' => $message, 'last_message' => $conversation->lastMessage, "success" => true]);
    }

    public function getMessages($receiverId)
    {
        $userId = auth()->user()->uid;
        $messages = $this->conversationRepositoryInterface->find($userId, $receiverId);

        return new ChatResource($messages);
    }

    public function getContactedUsers()
    {
        $userId = auth()->user()->uid;
        $messages = $this->conversationRepositoryInterface->getContactedUsers($userId);

        $messages = $messages->map(function ($message) {
            $other_user = $message->user1_uid === auth()->user()->uid ? $message->user2 : $message->user1;

            $message->is_online = Redis::exists('user-is-online-' . $other_user->id) ? true : false;
            $message->other_user = $other_user;

            return $message;
        });

        return ChatResource::collection($messages)
        ->additional([
            'success' => true,
        ]);;
    }

    public function createConversation(Request $request)
    {
        $userId = auth()->user()->uid;
        $receiverId = $request->receiver_id;

        // Kiểm tra đã có conversation chưa, nếu chưa thì tạo mới
        $conversation = $this->conversationRepositoryInterface->find($userId, $receiverId);

        if (!$conversation) {
            $conversation = $this->conversationRepositoryInterface->create([
                'user1_uid' => min($userId, $receiverId),
                'user2_uid' => max($userId, $receiverId),
            ]);
        }

        return response()->json([
            'success' => true,
            'conversation' => $conversation
        ]);
    }
}
