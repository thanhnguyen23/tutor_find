import api from './index';

const payApi = {
    createSepayBooking: async (payload) => {
        const response = await api.apiPost('bookings/sepay', payload);
        return response;
    },
    createPayment: async (payload) => {
        const response = await api.apiPost('vnpay/create-payment', payload);
        return response;
    },
    returnPayment: async (payload) => {
        const response = await api.apiPost('vnpay/return-payment', payload);
        return response;
    },
    ipnPayment: async (payload) => {
        const response = await api.apiPost('vnpay/ipn-payment', payload);
        return response;
    },
}

export default payApi;
