import moment from 'moment'

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
    formatDuration(hours) {
        const minutes = Math.round(hours * 60);
        const h = Math.floor(minutes / 60);
        const m = minutes % 60;

        if (h > 0 && m > 0) return `${h} giờ ${m} phút`;
        if (h > 0) return `${h} giờ`;
        return `${m} phút`;
    },
    getPriceRange(subjects) {
        let prices = [];

        console.log(subjects);

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
    },

    // Classroom time management helpers
    canStartClassroom(classroom) {
        if (!classroom.time_info.can_start) return false; // Fallback nếu không có time_info
        return true;
    },
    getTimeStatusText(timeInfo) {
        if (!timeInfo) return '';

        if (timeInfo.is_before_start) {
            const minutes = timeInfo.time_until_start_minutes;

            if (minutes > 1440) {
                const days = Math.floor(minutes / 1440);
                return `Còn ${days} ngày để bắt đầu`;
            } else if (minutes > 60) {
                const hours = Math.floor(minutes / 60);
                const remainingMinutes = minutes % 60;
                return `Còn ${hours}h ${remainingMinutes} phút để bắt đầu`;
            } else {
                return `Còn ${minutes} phút để bắt đầu`;
            }
        } else if (timeInfo.is_after_end) {
            return 'Lớp học đã kết thúc';
        } else {
            return 'Có thể bắt đầu/tham gia lớp học';
        }
    },

    getTimeStatusClass(timeInfo) {
        if (!timeInfo) return '';

        if (timeInfo.is_before_start) {
            return 'time-status-waiting';
        } else if (timeInfo.is_after_end) {
            return 'time-status-ended';
        } else {
            return 'time-status-ready';
        }
    },

    getTimeStatusMessage(timeInfo) {
        if (!timeInfo) return 'Không có thông tin thời gian';

        if (timeInfo.is_before_start) {
            const minutes = timeInfo.time_until_start_minutes;
            if (minutes > 1440) {
                const days = Math.floor(minutes / 1440);
                return `Lớp học chưa bắt đầu. Còn ${days} ngày để bắt đầu.`;
            } else if (minutes > 60) {
                const hours = Math.floor(minutes / 60);
                const remainingMinutes = minutes % 60;
                return `Lớp học chưa bắt đầu. Còn ${hours}h ${remainingMinutes} phút để bắt đầu.`;
            } else {
                return `Lớp học chưa bắt đầu. Còn ${minutes} phút để bắt đầu.`;
            }
        } else if (timeInfo.is_after_end) {
            return 'Lớp học đã kết thúc.';
        } else {
            return 'Lớp học chưa được phép bắt đầu.';
        }
    },

    getParticipantsClass(classroom) {
        const current = classroom.participants_count || 0;
        const max = classroom.max_participants || 2;
        return current >= max ? 'participants-full' : 'participants-available';
    },

    getStatusClass(status) {
        const statusClasses = {
            'pending': 'status-pending',
            'scheduled': 'status-scheduled',
            'started': 'status-started',
            'ended': 'status-ended',
            'cancelled': 'status-cancelled',
            'error': 'status-error'
        };
        return statusClasses[status] || '';
    },

    compareTime(a, b) {
        return (!a || !b ? 0 : a.localeCompare(b))
     },
    getDayOfWeek(date) {
        if (!date) return null
        const day = moment(date).day()
        return day === 0 ? 7 : day
    },
    dataIsNull(value) {
        return !value || (Array.isArray(value) && value.length === 0)
            ? 'Chưa có dữ liệu'
            : value
    },
    calcDurationHours(start, end) {
        const s = moment(start, 'HH:mm')
        const e = moment(end, 'HH:mm')
        if (!s.isValid() || !e.isValid() || e.isBefore(s)) return 0
        return moment.duration(e.diff(s)).asHours()
    },
    handleTimeSlot(timeSlots, selectedDate = null) {
        if (!timeSlots || !Array.isArray(timeSlots)) return []

        const now = new Date()
        const currentTime = moment(now).format('HH:mm')
        const currentDate = moment(now).format('YYYY-MM-DD')

        return timeSlots.map(slot => {
            let isDisabled = false

            // Mặc định disable tất cả các giờ đã qua
            if (slot.time) {
                const slotTime = moment(slot.time, 'HH:mm').format('HH:mm')

                // Nếu không có ngày được chọn hoặc ngày chọn là ngày hiện tại
                if (!selectedDate || selectedDate === currentDate) {
                    isDisabled = slotTime <= currentTime
                }
                // Nếu ngày chọn là ngày trong quá khứ, disable tất cả
                else if (selectedDate < currentDate) {
                    isDisabled = true
                }
            }

            return {
                ...slot,
                disabled: isDisabled
            }
        })
    }

}

export default helper;
