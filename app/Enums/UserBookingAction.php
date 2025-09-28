<?php
// app/Enums/UserBookingAction.php

namespace App\Enums;

enum UserBookingAction: string
{
    case WaitingPayment = 'waiting_payment';
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
    case AwaitingClass = 'awaiting_class';
    case Completed = 'completed';
    case Missed = 'missed';
}

?>
