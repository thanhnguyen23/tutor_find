// src/services/auth.js
import api, { setAuthToken } from './index.js';
import { useStore } from 'vuex';

export const register = async (payload) => {
    try {
        let response = await api.apiPost('auth/register', payload);
        let dataToken = response.token;

        setAuthToken(dataToken);

        return response;
    } catch (error) {
        throw error;
    }
};

export const login = async (payload) => {
    try {
        let response = await api.apiPost('auth/login', payload);
        let dataToken = response.token;

        setAuthToken(dataToken);

        return response;
    } catch (error) {
        throw error;
    }
};

export const sendOtp = async (payload) => {
    try {
        // payload gồm email
        const response = await api.apiPost('sendOtp', payload);
        return response;
    } catch (error) {
        throw error;
    }
};

export const verifyToken = async (token) => {
    try {
        const response = await api.apiPost('verify-token', {
            headers: { Authorization: `Bearer ${token}` },
        });
        return response.user;
    } catch (error) {
        throw error;
    }
};
