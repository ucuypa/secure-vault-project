import axios from './axios';

export default {
  getProfile() {
    return axios.get('/profile');
  },

  updateProfile(profileData) {
    return axios.put('/profile', profileData);
  }
};