const helper = {
    setDataLocalStorage(key, data) {
        localStorage.setItem(key, JSON.stringify(data));
    },
    getDataLocalStorage(key) {
        return JSON.parse(localStorage.getItem(key));
    },
    getUserDataLocalStorage() {
        return JSON.parse(sessionStorage.getItem("user"));
    },
    formatCurrency(amount) {
        return new Intl.NumberFormat("vi-VN", {
            style: "currency",
            currency: "VND",
        }).format(amount);
    },
    formatDate(date) {
        return new Date(date).toLocaleDateString("vi-VN", {
            day: "2-digit",
            month: "2-digit",
            year: "numeric",
        });
    },
    formatTime(time) {
        return new Date(time).toLocaleTimeString("vi-VN", {
            hour: "2-digit",
            minute: "2-digit",
        });
    },
    formatDateTime(dateTime) {
        return new Date(dateTime).toLocaleString("vi-VN", {
            day: "2-digit",
            month: "2-digit",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        });
    },
    getPriceRange(subjects) {
        let prices = [];

        subjects.forEach(subject => {
            if (subject.user_subject_levels) {
                subject.user_subject_levels.forEach(level => {
                    if (typeof level.price === 'number') {
                        prices.push(level.price);
                    }
                });
            }
        });

        if (prices.length === 0) return '';

        // Sắp xếp giá tăng dần
        prices.sort((a, b) => a - b);

        const mid = Math.floor(prices.length / 2);

        let median;
        if (prices.length % 2 === 0) {
            // Nếu số lượng chẵn, lấy trung bình 2 giá giữa
            median = (prices[mid - 1] + prices[mid]) / 2;
        } else {
            // Nếu lẻ, lấy giá giữa
            median = prices[mid];
        }

        return this.formatCurrency(median);
    },
    getFirstCharacterOfLastName(name) {
        if (!name) return '';
        const words = name.trim().split(' ');
        const lastWord = words[words.length - 1];
        return lastWord.charAt(0).toUpperCase();
    },
    formatRelativeTime(dateTime) {
        if (!dateTime) return '';

        const now = new Date();
        const targetDate = new Date(dateTime);
        const diffInMs = now - targetDate;

        const diffInMinutes = Math.floor(diffInMs / (1000 * 60));
        const diffInHours = Math.floor(diffInMs / (1000 * 60 * 60));
        const diffInDays = Math.floor(diffInMs / (1000 * 60 * 60 * 24));
        const diffInMonths = Math.floor(diffInMs / (1000 * 60 * 60 * 24 * 30)); // Ước lượng 1 tháng = 30 ngày
        const diffInYears = Math.floor(diffInMs / (1000 * 60 * 60 * 24 * 365)); // Ước lượng 1 năm = 365 ngày

        if (diffInMinutes < 1) {
            return 'Vừa xong';
        } else if (diffInMinutes < 60) {
            return `${diffInMinutes} phút trước`;
        } else if (diffInHours < 24) {
            return `${diffInHours} giờ trước`;
        } else if (diffInDays < 30) {
            return `${diffInDays} ngày trước`;
        } else if (diffInMonths < 12) {
            return `${diffInMonths} tháng trước`;
        } else {
            return `${diffInYears} năm trước`;
        }
    }
}

export default helper;
