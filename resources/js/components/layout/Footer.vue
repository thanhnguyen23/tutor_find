<template>
<footer class="footer">
    <div class="container">
        <div class="footer-top">
            <div class="footer-brand">
                <div class="logo">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-book-open h-5 w-5 text-primary-foreground"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                    <span class="logo-text">EduTutor</span>
                </div>
                <p class="brand-description">
                    Nền tảng kết nối gia sư chất lượng cao với học sinh và phụ huynh trên toàn quốc. Chúng tôi cam kết mang đến kết quả tốt nhất và phù hợp với từng cá nhân.
                </p>
                <div class="social-links">
                    <a href="#" class="social-link">
                        <svg class="icon" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                    </a>
                    <a href="#" class="social-link">
                        <svg class="icon" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                        </svg>
                    </a>
                    <a href="#" class="social-link">
                        <svg class="icon" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z" />
                        </svg>
                    </a>
                    <a href="#" class="social-link">
                        <svg class="icon" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                        </svg>
                    </a>
                </div>
            </div>
            <div class="footer-links">
                <div class="links-group">
                    <h3 class="group-title">Liên kết nhanh</h3>
                    <ul class="links-list">
                        <li><a href="#">Trang chủ</a></li>
                        <li><a href="#">Tìm gia sư</a></li>
                        <li><a href="#">Khóa học</a></li>
                        <li><a href="#">Đánh giá</a></li>
                        <li><a href="#">Liên hệ</a></li>
                    </ul>
                </div>
                <div class="links-group">
                    <h3 class="group-title">Môn học</h3>
                    <ul class="links-list">
                        <li><a href="#">Toán học</a></li>
                        <li><a href="#">Vật lý</a></li>
                        <li><a href="#">Hóa học</a></li>
                        <li><a href="#">Ngữ văn</a></li>
                        <li><a href="#">Tiếng Anh</a></li>
                    </ul>
                </div>
                <div class="links-group">
                    <h3 class="group-title">Liên hệ</h3>
                    <ul class="links-list">
                        <li>
                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh
                        </li>
                        <li>
                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            info@edututor.vn
                        </li>
                        <li>
                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            0123 456 789
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p class="copyright">© 2023 EduTutor. Tất cả các quyền được bảo lưu.</p>
            <div class="bottom-links">
                <a href="#">Điều khoản sử dụng</a>
                <a href="#">Chính sách bảo mật</a>
                <a href="#">Cookie</a>
            </div>
        </div>
    </div>
</footer>
</template>



<script setup>
    </script>


<style scoped>
.footer {
    background: white;
    color: black;
    padding: 64px 0 32px;
    border-top: 1px solid #9ca3af;
}

.container {
    max-width: 1300px;
    margin: 0 auto;
}

.footer-top {
    display: grid;
    grid-template-columns: 1.5fr 2fr;
    gap: 64px;
    margin-bottom: 48px;
}

.footer-brand {
    max-width: 400px;
}

.logo {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 24px;
}

.logo-image {
    height: 40px;
    width: auto;
}

.logo-text {
    font-size: 24px;
    font-weight: 700;
}

.brand-description {
    color: #9ca3af;
    line-height: 1.6;
    margin-bottom: 24px;
}

.social-links {
    display: flex;
    gap: 16px;
}

.social-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    color: black;
    transition: all 0.3s ease;
}

.social-link:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}

.social-link .icon {
    width: 20px;
    height: 20px;
}

.footer-links {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 32px;
}

.group-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 24px;
    color: black;
}

.links-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.links-list li {
    margin-bottom: 12px;
    color: #9ca3af;
}

.links-list li a {
    color: #9ca3af;
    text-decoration: none;
    transition: color 0.3s ease;
}

.links-list li a:hover {
    color: black;
}

.links-list li .icon {
    width: 20px;
    height: 20px;
    margin-right: 8px;
    color: black;
}

.footer-bottom {
    padding-top: 32px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.copyright {
    color: #9ca3af;
    font-size: 14px;
    margin-bottom: 0;
}

.bottom-links {
    display: flex;
    gap: 24px;
}

.bottom-links a {
    color: #9ca3af;
    text-decoration: none;
    font-size: 14px;
    transition: color 0.3s ease;
}

.bottom-links a:hover {
    color: black;
}

@media (max-width: 1024px) {
    .footer-top {
        grid-template-columns: 1fr;
        gap: 48px;
    }

    .footer-brand {
        max-width: none;
    }
}

@media (max-width: 768px) {
    .footer {
        padding: 48px 0 24px;
    }

    .footer-links {
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }

    .footer-bottom {
        flex-direction: column;
        gap: 16px;
        text-align: center;
    }

    .bottom-links {
        justify-content: center;
    }
}

@media (max-width: 640px) {
    .container {
        padding: 0 16px;
    }

    .footer-links {
        grid-template-columns: 1fr;
    }

    .bottom-links {
        flex-direction: column;
        gap: 12px;
    }
}
</style>
