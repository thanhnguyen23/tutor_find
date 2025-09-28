<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\UserBooking;
use App\Models\UserBookingComplaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConfigurationController extends Controller
{

    public function __construct(
    )
    {
    }

    public function getAllStatus() {
        try {
            $listStatusComplaint = UserBookingComplaint::$LIST_STATUS;
            $listStatusBooking = UserBooking::$LIST_STATUS;
            $listStatusPayment = Payment::$LIST_STATUS;

            return response()->json([
                'list_status_complaint' => $listStatusComplaint,
                'list_status_booking' => $listStatusBooking,
                'list_status_payment' => $listStatusPayment,
            ]);
        } catch (\Exception $e) {
            Log::error(__METHOD__, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Đã xảy ra lỗi, vui lòng thử lại sau'], 500);
        }
    }
}
