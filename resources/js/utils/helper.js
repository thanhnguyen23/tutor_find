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
}

export default helper;
