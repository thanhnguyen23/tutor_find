<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo buổi học bị lỡ</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #111827; }
        .container { max-width: 640px; margin: 0 auto; padding: 24px; }
        .card { border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; }
        .title { font-size: 20px; font-weight: 700; margin: 0 0 12px 0; }
        .muted { color: #6b7280; }
        .row { margin: 6px 0; }
        .label { color: #374151; font-weight: 600; }
        .value { color: #111827; }
        .footer { margin-top: 18px; color: #6b7280; font-size: 14px; }
    </style>
    </head>
<body>
    <div class="container">
        <div class="card">
            <div class="title">
                @if($isTutor)
                    Bạn đã bỏ lỡ một buổi học
                @else
                    Buổi học của bạn đã bị lỡ
                @endif
            </div>
            <p class="muted">Mã đặt lịch: <strong>{{ $booking->request_code }}</strong></p>

            <div class="row"><span class="label">Môn học:</span> <span class="value">{{ optional($booking->subject)->name ?? '-' }}</span></div>
            <div class="row"><span class="label">Cấp độ:</span> <span class="value">{{ optional($booking->educationLevel)->name ?? '-' }}</span></div>
            <div class="row"><span class="label">Ngày học:</span> <span class="value">{{ $booking->date }}</span></div>
            <div class="row"><span class="label">Khung giờ:</span> <span class="value">{{ optional($booking->timeSlot)->name ?? '-' }} - {{ $booking->end_time ? \Carbon\Carbon::parse($booking->end_time)->format('H:i') : '-' }}</span></div>

            @if($isTutor)
                <div class="row"><span class="label">Học viên:</span> <span class="value">{{ optional($booking->user)->full_name ?? '-' }}</span></div>
            @else
                <div class="row"><span class="label">Gia sư:</span> <span class="value">{{ optional($booking->tutor)->full_name ?? '-' }}</span></div>
            @endif

            <p class="footer">Nếu đây là nhầm lẫn, vui lòng liên hệ hỗ trợ hoặc phản hồi khiếu nại trong hệ thống.</p>
        </div>
    </div>
</body>
</html>


