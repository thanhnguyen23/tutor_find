import store from '../store/index';

const notification = {
    notification(data) {
        store.dispatch('updateShowNotification', {
            show: true,
            message: data.message,
            type: data.type,
        });

        setTimeout(() => {
            store.dispatch('updateShowNotification', {
                show: false,
                message: '',
                type: data.type,
            });
        }, data.time);
    },
    error(message) {
        const data = {
            message: message,
            type: 'error',
            time: 3000,
        }
        this.notification(data);
    },
    success(message) {
        const data = {
            message: message,
            type: 'success',
            time: 3000,
        }
        this.notification(data);
    },
    info(message) {
        const data = {
            message: message,
            type: 'info',
            time: 3000,
        }
        this.notification(data);
    },
    warring(message) {
        const data = {
            message: message,
            type: 'warning',
            time: 3000,
        }
        this.notification(data);
    },
    errorNotEmpty(message) {
        const data = {
            message: message ?? "Không được để trống, vui lòng nhập dữ liệu",
            type: 'error',
            time: 3000,
        }
        this.notification(data);
    },
}

export default notification;
