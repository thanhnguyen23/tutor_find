<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            // **Khoa học tự nhiên (category_subject_id: 1)**
            ['slug' => 'toan', 'name' => 'Toán', 'image' => '📐', 'category_subject_id' => 1],
            ['slug' => 'dai-so', 'name' => 'Đại số', 'image' => '🔢', 'category_subject_id' => 1],
            ['slug' => 'hinh-hoc', 'name' => 'Hình học', 'image' => '📏', 'category_subject_id' => 1],
            ['slug' => 'giai-tich', 'name' => 'Giải tích', 'image' => '📈', 'category_subject_id' => 1],
            ['slug' => 'toan-cao-cap', 'name' => 'Toán cao cấp', 'image' => '📐', 'category_subject_id' => 1],
            ['slug' => 'xac-suat-thong-ke', 'name' => 'Xác suất - Thống kê', 'image' => '📈', 'category_subject_id' => 1],
            ['slug' => 'vat-ly', 'name' => 'Vật lý', 'image' => '⚡', 'category_subject_id' => 1],
            ['slug' => 'vat-ly-dai-cuong', 'name' => 'Vật lý đại cương', 'image' => '⚡', 'category_subject_id' => 1],
            ['slug' => 'hoa-hoc', 'name' => 'Hóa học', 'image' => '🧪', 'category_subject_id' => 1],
            ['slug' => 'hoa-hoc-dai-cuong', 'name' => 'Hóa học đại cương', 'image' => '🧪', 'category_subject_id' => 1],
            ['slug' => 'sinh-hoc', 'name' => 'Sinh học', 'image' => '🌱', 'category_subject_id' => 1],
            ['slug' => 'khoa-hoc', 'name' => 'Khoa học', 'image' => '🔬', 'category_subject_id' => 1],

            // **Khoa học xã hội (category_subject_id: 2)**
            ['slug' => 'lich-su', 'name' => 'Lịch sử', 'image' => '🏯', 'category_subject_id' => 2],
            ['slug' => 'dia-ly', 'name' => 'Địa lý', 'image' => '🌍', 'category_subject_id' => 2],
            ['slug' => 'lich-su-va-dia-ly', 'name' => 'Lịch sử và Địa lý', 'image' => '🏯', 'category_subject_id' => 2],
            ['slug' => 'tu-nhien-va-xa-hoi', 'name' => 'Tự nhiên và Xã hội', 'image' => '🌳', 'category_subject_id' => 2],
            ['slug' => 'gdcd', 'name' => 'Giáo dục công dân', 'image' => '⚖️', 'category_subject_id' => 2],
            ['slug' => 'triet-hoc', 'name' => 'Triết học', 'image' => '🤓', 'category_subject_id' => 2],
            ['slug' => 'kinh-te-chinh-tri', 'name' => 'Kinh tế chính trị', 'image' => '📊', 'category_subject_id' => 2],
            ['slug' => 'chu-nghia-xa-hoi-khoa-hoc', 'name' => 'Chủ nghĩa xã hội khoa học', 'image' => '⭐', 'category_subject_id' => 2],
            ['slug' => 'lich-su-dang', 'name' => 'Lịch sử Đảng', 'image' => '🏛️', 'category_subject_id' => 2],
            ['slug' => 'tu-tuong-ho-chi-minh', 'name' => 'Tư tưởng Hồ Chí Minh', 'image' => '🇻🇳', 'category_subject_id' => 2],
            ['slug' => 'quoc-phong-an-ninh', 'name' => 'Quốc phòng - An ninh', 'image' => '🛡️', 'category_subject_id' => 2],

            // **Ngôn ngữ (category_subject_id: 3)**
            ['slug' => 'tieng-viet', 'name' => 'Tiếng Việt', 'image' => '📝', 'category_subject_id' => 3],
            ['slug' => 'ngu-van', 'name' => 'Ngữ văn', 'image' => '📝', 'category_subject_id' => 3],
            ['slug' => 'ngoai-ngu', 'name' => 'Ngoại ngữ', 'image' => '🌍', 'category_subject_id' => 3],
            ['slug' => 'tieng-anh', 'name' => 'Tiếng Anh', 'image' => '🌍', 'category_subject_id' => 3],

            // **Công nghệ (category_subject_id: 4)**
            ['slug' => 'tin-hoc', 'name' => 'Tin học', 'image' => '💻', 'category_subject_id' => 4],
            ['slug' => 'tin-hoc-dai-cuong', 'name' => 'Tin học đại cương', 'image' => '💻', 'category_subject_id' => 4],
            ['slug' => 'cong-nghe', 'name' => 'Công nghệ', 'image' => '🔧', 'category_subject_id' => 4],
            ['slug' => 'lap-trinh-co-ban', 'name' => 'Lập trình cơ bản', 'image' => '💻', 'category_subject_id' => 4],
            ['slug' => 'cau-truc-du-lieu', 'name' => 'Cấu trúc dữ liệu', 'image' => '📊', 'category_subject_id' => 4],
            ['slug' => 'giai-thuat', 'name' => 'Giải thuật', 'image' => '🧠', 'category_subject_id' => 4],
            ['slug' => 'php', 'name' => 'PHP', 'image' => '🐘', 'category_subject_id' => 4],
            ['slug' => 'python', 'name' => 'Python', 'image' => '🐍', 'category_subject_id' => 4],
            ['slug' => 'javascript', 'name' => 'JavaScript', 'image' => '🟨', 'category_subject_id' => 4],
            ['slug' => 'java', 'name' => 'Java', 'image' => '☕', 'category_subject_id' => 4],
            ['slug' => 'c-sharp', 'name' => 'C#', 'image' => '🔷', 'category_subject_id' => 4],
            ['slug' => 'c-plus-plus', 'name' => 'C++', 'image' => '🔵', 'category_subject_id' => 4],
            ['slug' => 'co-so-du-lieu', 'name' => 'Cơ sở dữ liệu', 'image' => '🗄️', 'category_subject_id' => 4],
            ['slug' => 'mang-may-tinh', 'name' => 'Mạng máy tính', 'image' => '🌐', 'category_subject_id' => 4],
            ['slug' => 'he-dieu-hanh', 'name' => 'Hệ điều hành', 'image' => '🖥️', 'category_subject_id' => 4],
            ['slug' => 'tri-tue-nhan-tao', 'name' => 'Trí tuệ nhân tạo', 'image' => '🤖', 'category_subject_id' => 4],
            ['slug' => 'an-toan-thong-tin', 'name' => 'An toàn thông tin', 'image' => '🔒', 'category_subject_id' => 4],
            ['slug' => 'phat-trien-ung-dung-web', 'name' => 'Phát triển ứng dụng web', 'image' => '🌐', 'category_subject_id' => 4],
            ['slug' => 'phat-trien-ung-dung-di-dong', 'name' => 'Phát triển ứng dụng di động', 'image' => '📱', 'category_subject_id' => 4],
            ['slug' => 'kien-truc-may-tinh', 'name' => 'Kiến trúc máy tính', 'image' => '🖥️', 'category_subject_id' => 4],
            ['slug' => 'do-hoa-may-tinh', 'name' => 'Đồ họa máy tính', 'image' => '🎨', 'category_subject_id' => 4],

            // **Nghệ thuật (category_subject_id: 5)**
            ['slug' => 'my-thuat', 'name' => 'Mỹ thuật', 'image' => '🎨', 'category_subject_id' => 5],
            ['slug' => 'am-nhac', 'name' => 'Âm nhạc', 'image' => '🎵', 'category_subject_id' => 5],
            ['slug' => 'thiet-ke-do-hoa', 'name' => 'Thiết kế đồ họa', 'image' => '🎨', 'category_subject_id' => 5],
            ['slug' => 'thiet-ke-thoi-trang', 'name' => 'Thiết kế thời trang', 'image' => '👗', 'category_subject_id' => 5],
            ['slug' => 'thiet-ke-noi-that', 'name' => 'Thiết kế nội thất', 'image' => '🏠', 'category_subject_id' => 5],
            ['slug' => 'am-nhac-hoc', 'name' => 'Âm nhạc học', 'image' => '🎵', 'category_subject_id' => 5],
            ['slug' => 'dien-anh', 'name' => 'Điện ảnh', 'image' => '🎬', 'category_subject_id' => 5],

            // **Thể dục (category_subject_id: 6)**
            ['slug' => 'the-duc', 'name' => 'Thể dục', 'image' => '🏃', 'category_subject_id' => 6],

            // **Kỹ năng (category_subject_id: 7)**
            ['slug' => 'dao-duc', 'name' => 'Đạo đức', 'image' => '🤝', 'category_subject_id' => 7],
            ['slug' => 'hoat-dong-trai-nghiem', 'name' => 'Hoạt động trải nghiệm', 'image' => '🌟', 'category_subject_id' => 7],
            ['slug' => 'ky-nang-mem', 'name' => 'Kỹ năng mềm', 'image' => '🌟', 'category_subject_id' => 7],

            // **Kinh tế (category_subject_id: 8)**
            ['slug' => 'kinh-te-vi-mo', 'name' => 'Kinh tế vi mô', 'image' => '📉', 'category_subject_id' => 8],
            ['slug' => 'kinh-te-v-mo', 'name' => 'Kinh tế vĩ mô', 'image' => '📈', 'category_subject_id' => 8],
            ['slug' => 'ke-toan-tai-chinh', 'name' => 'Kế toán tài chính', 'image' => '💰', 'category_subject_id' => 8],
            ['slug' => 'ke-toan-quan-tri', 'name' => 'Kế toán quản trị', 'image' => '📊', 'category_subject_id' => 8],
            ['slug' => 'tai-chinh-doanh-nghiep', 'name' => 'Tài chính doanh nghiệp', 'image' => '💸', 'category_subject_id' => 8],
            ['slug' => 'quan-tri-kinh-doanh', 'name' => 'Quản trị kinh doanh', 'image' => '🏢', 'category_subject_id' => 8],
            ['slug' => 'marketing', 'name' => 'Marketing', 'image' => '📣', 'category_subject_id' => 8],
            ['slug' => 'quan-tri-nhan-luc', 'name' => 'Quản trị nhân lực', 'image' => '👥', 'category_subject_id' => 8],
            ['slug' => 'thuong-mai-dien-tu', 'name' => 'Thương mại điện tử', 'image' => '🛒', 'category_subject_id' => 8],
            ['slug' => 'kinh-te-quoc-te', 'name' => 'Kinh tế quốc tế', 'image' => '🌍', 'category_subject_id' => 8],
            ['slug' => 'luat-kinh-doanh', 'name' => 'Luật kinh doanh', 'image' => '⚖️', 'category_subject_id' => 8],

            // **Kỹ thuật (category_subject_id: 9)**
            ['slug' => 'ky-thuat-dien', 'name' => 'Kỹ thuật điện', 'image' => '🔌', 'category_subject_id' => 9],
            ['slug' => 'dien-tu', 'name' => 'Điện tử', 'image' => '💡', 'category_subject_id' => 9],
            ['slug' => 'ky-thuat-co-khi', 'name' => 'Kỹ thuật cơ khí', 'image' => '⚙️', 'category_subject_id' => 9],
            ['slug' => 'ky-thuat-xay-dung', 'name' => 'Kỹ thuật xây dựng', 'image' => '🏗️', 'category_subject_id' => 9],
            ['slug' => 'co-dien-tu', 'name' => 'Cơ điện tử', 'image' => '🤖', 'category_subject_id' => 9],
            ['slug' => 'ky-thuat-o-to', 'name' => 'Kỹ thuật ô tô', 'image' => '🚗', 'category_subject_id' => 9],
            ['slug' => 'ky-thuat-hoa-hoc', 'name' => 'Kỹ thuật hóa học', 'image' => '🧪', 'category_subject_id' => 9],
            ['slug' => 'ky-thuat-moi-truong', 'name' => 'Kỹ thuật môi trường', 'image' => '🌱', 'category_subject_id' => 9],
            ['slug' => 'ky-thuat-vat-lieu', 'name' => 'Kỹ thuật vật liệu', 'image' => '🛠️', 'category_subject_id' => 9],

            // **Y học (category_subject_id: 10)**
            ['slug' => 'giai-phau-hoc', 'name' => 'Giải phẫu học', 'image' => '🦴', 'category_subject_id' => 10],
            ['slug' => 'sinh-ly-hoc', 'name' => 'Sinh lý học', 'image' => '❤️', 'category_subject_id' => 10],
            ['slug' => 'hoa-sinh', 'name' => 'Hóa sinh', 'image' => '🧬', 'category_subject_id' => 10],
            ['slug' => 'vi-sinh-vat', 'name' => 'Vi sinh vật', 'image' => '🔬', 'category_subject_id' => 10],
            ['slug' => 'benh-ly-hoc', 'name' => 'Bệnh lý học', 'image' => '🏥', 'category_subject_id' => 10],
            ['slug' => 'duoc-ly-hoc', 'name' => 'Dược lý học', 'image' => '💊', 'category_subject_id' => 10],
            ['slug' => 'noi-khoa', 'name' => 'Nội khoa', 'image' => '🩺', 'category_subject_id' => 10],
            ['slug' => 'ngoai-khoa', 'name' => 'Ngoại khoa', 'image' => '🔪', 'category_subject_id' => 10],
            ['slug' => 'san-phu-khoa', 'name' => 'Sản phụ khoa', 'image' => '👶', 'category_subject_id' => 10],
            ['slug' => 'nhi-khoa', 'name' => 'Nhi khoa', 'image' => '🍼', 'category_subject_id' => 10],

            // **Luật (category_subject_id: 11)**
            ['slug' => 'phap-luat-dai-cuong', 'name' => 'Pháp luật đại cương', 'image' => '⚖️', 'category_subject_id' => 11],
            ['slug' => 'luat-hien-phap', 'name' => 'Luật hiến pháp', 'image' => '📜', 'category_subject_id' => 11],
            ['slug' => 'luat-dan-su', 'name' => 'Luật dân sự', 'image' => '⚖️', 'category_subject_id' => 11],
            ['slug' => 'luat-hinh-su', 'name' => 'Luật hình sự', 'image' => '🚨', 'category_subject_id' => 11],
            ['slug' => 'luat-thuong-mai', 'name' => 'Luật thương mại', 'image' => '🏢', 'category_subject_id' => 11],
            ['slug' => 'luat-lao-dong', 'name' => 'Luật lao động', 'image' => '👷', 'category_subject_id' => 11],
            ['slug' => 'luat-quoc-te', 'name' => 'Luật quốc tế', 'image' => '🌍', 'category_subject_id' => 11],
            ['slug' => 'luat-hanh-chinh', 'name' => 'Luật hành chính', 'image' => '🏛️', 'category_subject_id' => 11],

            // **Giáo dục (category_subject_id: 12)**
            ['slug' => 'tam-ly-hoc-giao-duc', 'name' => 'Tâm lý học giáo dục', 'image' => '🧠', 'category_subject_id' => 12],
            ['slug' => 'giao-duc-hoc', 'name' => 'Giáo dục học', 'image' => '📚', 'category_subject_id' => 12],
            ['slug' => 'phuong-phap-day-hoc', 'name' => 'Phương pháp dạy học', 'image' => '👩‍🏫', 'category_subject_id' => 12],
            ['slug' => 'quan-ly-giao-duc', 'name' => 'Quản lý giáo dục', 'image' => '🏫', 'category_subject_id' => 12],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}
