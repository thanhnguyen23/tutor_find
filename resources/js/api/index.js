import axios from 'axios';

const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const token = sessionStorage.getItem('token');

const axiosInstance = axios.create({
    baseURL: "http://127.0.0.1:8000",
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    timeout: 10000,
    withCredentials: true,
});

axiosInstance.interceptors.response.use(
    (response) => response,
    (error) => {
        return Promise.reject(handleError(error));
    }
);


if (csrfToken) {
    axiosInstance.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}

if (token) {
    axiosInstance.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

const handleResponse = (response) => {
    return response.data;
};

// Hàm xử lý lỗi chung
const handleError = (error) => {
    if (error?.data) {
        throw error.data;
    } else if (error?.response?.data) {
        throw error.response.data;
    } else {
        throw error;
    }
}

const api = {
    async apiGet(url, params) {
        try {
            const response = await axiosInstance.get('/api/' + url, { params });
            return handleResponse(response);
        } catch (e) {
            return handleError(e);
        }
    },

    async apiPost(url, params) {
        try {
            const response = await axiosInstance.post('/api/' + url, params);
            return handleResponse(response);
        } catch (e) {
            return handleError(e);
        }
    },

    async apiPut(url, params) {
        try {
            const response = await axiosInstance.put('/api/' + url, params);
            return handleResponse(response);
        } catch (e) {
            return handleError(e);
        }
    },

    async apiDelete(url) {
        try {
            const response = await axiosInstance.delete('/api/' + url);
            return handleResponse(response);
        } catch (e) {
            return handleError(e);
        }
    },

    async get(url, params) {
        try {
            const response = await axiosInstance.get(url, { params });
            return handleResponse(response);
        } catch (e) {
            return handleError(e);
        }
    },

    async post(url, params) {
        try {
            const response = await axiosInstance.post(url, params);
            return handleResponse(response);
        } catch (e) {
            return handleError(e);
        }
    },

    async apiPostFormData(url, params) {
        try {
            const response = await axiosInstance.post('/api/' + url, params, {
                headers: {
                    'Content-Type':'multipart/form-data',
                },
            });
            return handleResponse(response);
        } catch (e) {
            return handleError(e);
        }
    },

    async apiPutFormData(url, params) {
        try {
            const response = await axiosInstance.put('/api/' + url, params, {
                headers: {
                    'Content-Type':'multipart/form-data',
                },
            });
            return handleResponse(response);
        } catch (e) {
            return handleError(e);
        }
    }
};

export const setAuthToken = (token) => {
    if (token) {
        axiosInstance.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    } else {
        delete axiosInstance.defaults.headers.common['Authorization'];
    }
};

export default api;
