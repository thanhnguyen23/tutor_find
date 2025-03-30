import api from './index';

const userApi = {
    getUser: async () => {
        const response = await api.get('/user');
        return response.data;
    },
    updateUser: async (payload) => {
        const response = await api.put('/user', payload);
        return response.data;
    },
    updatePassword: async (payload) => {
        const response = await api.put('/user/password', payload);
        return response.data;
    },
    updateAvatar: async (payload) => {
        const response = await api.put('/user/avatar', payload);
        return response.data;
    },
}
export default userApi;
