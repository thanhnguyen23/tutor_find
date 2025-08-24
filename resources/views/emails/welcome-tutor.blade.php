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
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Chào mừng đến với EduTutor!</h1>
        </div>

        <p>Xin chào {{ $userName }},</p>

        <p>Chúng tôi rất vui mừng chào đón bạn tham gia cộng đồng gia sư của EduTutor. Để bắt đầu nhận được các yêu cầu từ học sinh, bạn cần hoàn thiện hồ sơ gia sư của mình.</p>

        <p>Hồ sơ đầy đủ sẽ giúp bạn:</p>
        <ul>
            <li>Tăng khả năng được học sinh lựa chọn</li>
            <li>Xây dựng uy tín và độ tin cậy</li>
            <li>Thể hiện chuyên môn và kinh nghiệm của bạn</li>
        </ul>

        <p style="text-align: center;">
            <a href="{{ $profileUrl }}" class="button">Hoàn thiện hồ sơ ngay</a>
        </p>

        <p>Nếu bạn cần hỗ trợ, đừng ngần ngại liên hệ với chúng tôi qua email hoặc hotline.</p>

        <div class="footer">
            <p>Trân trọng,<br>Đội ngũ EduTutor</p>
        </div>
    </div>
</body>
</html>
