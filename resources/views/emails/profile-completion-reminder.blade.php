<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #000;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .progress {
            background-color: #f3f4f6;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .progress-bar {
            background-color: #e5e7eb;
            height: 20px;
            border-radius: 10px;
            position: relative;
            overflow: hidden;
        }
        .progress-fill {
            background-color: #000;
            height: 100%;
            width: {{ $completionPercent }}%;
            transition: width 0.3s ease;
        }
        .checklist {
            list-style: none;
            padding: 0;
        }
        .checklist li {
            margin: 10px 0;
            padding-left: 25px;
            position: relative;
        }
        .checklist li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #22c55e;
        }
        .checklist li.incomplete:before {
            content: "×";
            color: #ef4444;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
        .warning {
            color: #ef4444;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Hoàn thiện hồ sơ gia sư của bạn</h1>
        </div>

        <p>Xin chào {{ $userName }},</p>

        <p>Chúng tôi nhận thấy hồ sơ gia sư của bạn vẫn chưa được hoàn thiện. Để có thể nhận được các yêu cầu từ học sinh, bạn cần hoàn thành các thông tin còn thiếu.</p>

        <div class="progress">
            <h3>Tiến độ hoàn thiện: {{ $completionPercent }}%</h3>
            <div class="progress-bar">
                <div class="progress-fill"></div>
            </div>
        </div>

        <h3>Danh sách kiểm tra:</h3>
        <ul class="checklist">
            <li class="{{ $completionDetails['personal_info'] ? '' : 'incomplete' }}">
                Thông tin cá nhân {{ $completionDetails['personal_info'] ? '(Đã hoàn thiện)' : '(Chưa hoàn thiện)' }}
            </li>
            <li class="{{ $completionDetails['subjects'] ? '' : 'incomplete' }}">
                Môn học giảng dạy {{ $completionDetails['subjects'] ? '(Đã hoàn thiện)' : '(Chưa hoàn thiện)' }}
            </li>
            <li class="{{ $completionDetails['education'] ? '' : 'incomplete' }}">
                Học vấn {{ $completionDetails['education'] ? '(Đã hoàn thiện)' : '(Chưa hoàn thiện)' }}
            </li>
            <li class="{{ $completionDetails['experience'] ? '' : 'incomplete' }}">
                Kinh nghiệm giảng dạy {{ $completionDetails['experience'] ? '(Đã hoàn thiện)' : '(Chưa hoàn thiện)' }}
            </li>
            <li class="{{ $completionDetails['schedule'] ? '' : 'incomplete' }}">
                Lịch dạy {{ $completionDetails['schedule'] ? '(Đã hoàn thiện)' : '(Chưa hoàn thiện)' }}
            </li>
            <li class="{{ $completionDetails['languages'] ? '' : 'incomplete' }}">
                Ngôn ngữ {{ $completionDetails['languages'] ? '(Đã hoàn thiện)' : '(Chưa hoàn thiện)' }}
            </li>
        </ul>

        <p style="text-align: center;">
            <a href="{{ $profileUrl }}" class="button">Hoàn thiện hồ sơ ngay</a>
        </p>

        @if($remainingEmails > 0)
            <p>Chúng tôi sẽ gửi thêm {{ $remainingEmails }} email nhắc nhở nữa nếu hồ sơ chưa được hoàn thiện.</p>
        @else
            <p class="warning">Đây là email nhắc nhở cuối cùng về việc hoàn thiện hồ sơ.</p>
        @endif

        <p>Nếu bạn cần hỗ trợ, đừng ngần ngại liên hệ với chúng tôi qua email hoặc hotline.</p>

        <div class="footer">
            <p>Trân trọng,<br>Đội ngũ EduTutor</p>
        </div>
    </div>
</body>
</html>
