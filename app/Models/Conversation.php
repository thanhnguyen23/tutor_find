<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $table = 'conversation';

    protected $fillable = ['user1_uid', 'user2_uid', 'last_message_id'];

    public function user1()
    {
        return $this->belongsTo(User::class, 'user1_uid', 'uid');
    }

    public function user2()
    {
        return $this->belongsTo(User::class, 'user2_uid', 'uid');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage()
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }
}
