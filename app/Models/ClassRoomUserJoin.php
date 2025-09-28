<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoomUserJoin extends Model
{
    use HasFactory;

    protected $table = 'class_room_user_join';

    protected $fillable = ['uid', 'class_room_id'];

    public function classRoom() {
        return $this->belongsTo(ClassRoom::class, 'class_room_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'uid', 'uid');
    }
}
