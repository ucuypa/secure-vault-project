import axios from './axios';

const authService = {
    // Fungsi untuk pendaftaran user baru
    async register(userData) {
        const response = await axios.post('/register', userData);
        return response.data;
    },

    // Fungsi login: Menyimpan token ke localStorage jika sukses
    async login(credentials) {
        const response = await axios.post('/login', credentials);
        if (response.data.token) {
            localStorage.setItem('token', response.data.token);
            localStorage.setItem('user', JSON.stringify(response.data.user));
        }
        return response.data;
    },

    // Fungsi logout: Membersihkan session
    logout() {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        window.location.href = '/login';
    }
};

export default authService;