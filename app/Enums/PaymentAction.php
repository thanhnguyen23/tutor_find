<?php
// app/Enums/UserBookingAction.php

namespace App\Enums;

enum PaymentAction: string
{
    case Pending = 'pending';
    case Success = 'success';
    case Failed = 'failed';
    case Refunded = 'refunded';
    case NonPayment = 'non_payment';
}

?>
