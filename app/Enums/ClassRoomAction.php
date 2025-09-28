<?php
// app/Enums/ClassRoomAction.php

namespace App\Enums;

enum ClassRoomAction: string
{
    case Pending = 'pending';
    case Scheduled = 'scheduled';
    case Started = 'started';
    case Ended = 'ended';
    case Cancelled = 'cancelled';
    case Error = 'error';
    case Missed = 'missed';
}

?>
