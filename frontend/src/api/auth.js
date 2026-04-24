import axios from './axios';

export const authService = {
    async register(userData) {
        const response = await axios.post('/register', userData);
        return response.data;
    },

    async login(credentials) {
        const response = await axios.post('/login', credentials);
        if (response.data.token) {
            localStorage.setItem('token', response.data.token);
            localStorage.setItem('user', JSON.stringify(response.data.user));
        }
        return response.data;
    },

    logout() {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        window.location.href = '/login';
    }
};