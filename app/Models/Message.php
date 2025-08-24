<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'message';

    protected $fillable = ['conversation_id', 'sender_id', 'receiver_id', 'content', 'is_read'];

    public function receiver() {
        return $this->hasOne(User::class, 'receiver_id', 'uid');
    }

    public function sender() {
        return $this->hasOne(User::class, 'sender_id', 'uid');
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
