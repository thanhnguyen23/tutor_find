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
                type: 'error',
            });
        }, data.time);
    },
    notificationError(message) {
        const data = {
            message: message,
            type: 'error',
            time: 3000,
        }
        this.notification(data);
    },
    notificationSuccess(message) {
        const data = {
            message: message,
            type: 'success',
            time: 3000,
        }
        this.notification(data);
    },
    notificationErrorNotEmpty(message) {
        const data = {
            message: message ?? "Không được để trống, vui lòng nhập dữ liệu",
            type: 'error',
            time: 3000,
        }
        this.notification(data);
    },
}

export default notification;
