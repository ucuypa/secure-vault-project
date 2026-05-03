import axios from './axios';

const activityService = {
    // Fetch all activity logs for the logged-in user
    async getLogs() {
        // Note: Check with Ucup if the endpoint is '/activity-logs' or '/activities'
        const response = await axios.get('/activity'); 
        return response.data;
    }
};

export default activityService;