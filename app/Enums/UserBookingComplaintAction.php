<?php
// app/Enums/UserBookingAction.php

namespace App\Enums;

enum UserBookingComplaintAction: string
{
    case Pending = 'pending';
    case Resolved = 'resolved';
    case Rejected = 'rejected';
    case UnderReview = 'under_review';
}

?>
