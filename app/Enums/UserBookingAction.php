<?php
// app/Enums/UserBookingAction.php

namespace App\Enums;

enum UserBookingAction: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
    case Completed = 'completed';
}

?>
