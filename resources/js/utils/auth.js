// src/utils/auth.js
import api from '../api';
export function isAuthenticated() {
    const token = sessionStorage.getItem('token');
    const user = sessionStorage.getItem('user');

    // Kiểm tra cả token và user đều tồn tại
    return token && user;
}