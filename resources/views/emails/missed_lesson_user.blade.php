<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xin lỗi về buổi học bị lỡ</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #111827; background: #fbfdff; }
        .container { max-width: 640px; margin: 0 auto; padding: 24px; }
        .header { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
        .brand-left { display: flex; align-items: center; gap: 10px; }
        .brand-icon { width: 40px; height: 40px; border-radius: 10px; background: #0f172a; color: #fff; display: flex; align-items: center; justify-content: center; }
        .brand-name { font-weight: 800; margin: 0; font-size: 18px; }
        .brand-email { color: #6b7280; font-size: 12px; margin: 0; }
        .code-chip { background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 8px; padding: 6px 10px; font-size: 12px; color: #374151; white-space: nowrap; }

        .card { border: 1px solid #e5e7eb; border-radius: 12px; background: #fff; }
        .card-section { padding: 16px 20px; }
        .separate { border-top: 1px solid #e5e7eb; }

        .alert { display: flex; align-items: center; gap: 10px; background: #f8fafc; border-left: 4px solid #0f172a; padding: 12px 14px; border-radius: 10px; }
        .alert-title { font-weight: 800; margin: 0; font-size: 16px; }
        .alert-desc { margin: 2px 0 0 0; color: #6b7280; font-size: 13px; }

        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .item { display: grid; gap: 6px; }
        .item .label { color: #6b7280; font-size: 13px; text-transform: uppercase; letter-spacing: .4px; }
        .item .value { color: #111827; font-weight: 600; }

        .footer-note { color: #6b7280; font-size: 13px; }
        .actions { display: flex; gap: 10px; }
        .btn { display: inline-block; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; }
        .btn-primary { background: #0f172a; color: #fff; }
        .btn-secondary { background: #fff; color: #111827; border: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="brand-left">
                <div class="brand-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5Z" stroke="currentColor" stroke-width="2"/>
                        <path d="M22 6 12.97 12.46a2 2 0 0 1-1.94 0L2 6" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </div>
                <div>
                    <p class="brand-name">Hệ thống Quản lý Học tập</p>
                    <p class="brand-email">education@university.edu.vn</p>
                </div>
            </div>
            <div class="code-chip">{{ $booking->request_code }}</div>
        </div>

        <div class="card">
            <div class="card-section">
                <div class="alert">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 9v4" stroke="#0f172a" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="12" cy="16" r="1" fill="#0f172a"/>
                        <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke="#0f172a" stroke-width="2" fill="none"/>
                    </svg>
                    <div>
                        <p class="alert-title">Xin lỗi: Buổi học của bạn đã bị lỡ</p>
                        <p class="alert-desc">Chúng tôi rất tiếc về sự bất tiện này. Vui lòng xem lại thông tin bên dưới.</p>
                    </div>
                </div>
            </div>

            <div class="card-section separate">
                <div class="grid">
                    <div class="item">
                        <span class="label">Môn học</span>
                        <span class="value">{{ optional($booking->subject)->name ?? '-' }}</span>
                    </div>
                    <div class="item">
                        <span class="label">Cấp độ</span>
                        <span class="value">{{ optional($booking->educationLevel)->name ?? '-' }}</span>
                    </div>
                    <div class="item">
                        <span class="label">Ngày học</span>
                        <span class="value">{{ $booking->date }}</span>
                    </div>
                    <div class="item">
                        <span class="label">Thời gian</span>
                        <span class="value">{{ optional($booking->timeSlot)->name ?? '-' }} - {{ $booking->end_time ? \Carbon\Carbon::parse($booking->end_time)->format('H:i') : '-' }}</span>
                    </div>
                    <div class="item" style="grid-column: 1 / -1;">
                        <span class="label">Gia sư</span>
                        <span class="value">{{ optional($booking->tutor)->full_name ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="card-section separate">
                @if($isFree)
                    <p class="footer-note">Buổi học này là miễn phí nên sẽ không phát sinh hoàn tiền. Chúng tôi sẵn sàng hỗ trợ bạn sắp xếp lại lịch học.</p>
                @else
                    <p class="footer-note">Hệ thống sẽ tiến hành hoàn tiền cho buổi học này trong thời gian sớm nhất theo phương thức thanh toán bạn đã chọn.</p>
                @endif
                <div class="actions">
                    <a class="btn btn-primary" href="/booking/manager">Phản hồi ngay</a>
                    <a class="btn btn-secondary" href="/booking/manager">Xem lịch học</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


