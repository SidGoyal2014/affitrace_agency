// Detect environment or use fixed XAMPP path
// const API_BASE_URL = 'http://localhost:8000/api'; // Laravel Serve
const API_BASE_URL = 'http://localhost/affitrace/affitrace_REST/1/REST-BACKEND/public/api'; // XAMPP

const API = {
    // --- Authentication ---

    login: async (email, password, user_type, eai) => {
        try {
            const response = await fetch(`${API_BASE_URL}/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email, password, user_type, eai })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || data.error || 'Login failed');
            }

            // Save tokens and agency ID
            localStorage.setItem('access_token', data.access_token);
            localStorage.setItem('refresh_token', data.refresh_token);
            localStorage.setItem('user', JSON.stringify(data.user));
            localStorage.setItem('agency_id', eai);

            return data;
        } catch (error) {
            console.error('Login Error:', error);
            throw error;
        }
    },

    refreshToken: async () => {
        const refreshToken = localStorage.getItem('refresh_token');
        if (!refreshToken) {
            throw new Error('No refresh token available');
        }

        try {
            const response = await fetch(`${API_BASE_URL}/refresh-token`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${refreshToken}`,
                    'X-Agency-ID': localStorage.getItem('agency_id')
                }
            });

            const data = await response.json();

            if (!response.ok) {
                if (response.status === 401 || response.status === 403) {
                    // Refresh failed (expired/revoked), force logout
                    API.logout();
                }
                throw new Error(data.message || 'Token refresh failed');
            }

            // Update tokens
            localStorage.setItem('access_token', data.access_token);
            localStorage.setItem('refresh_token', data.refresh_token);

            return data.access_token;
        } catch (error) {
            console.error('Refresh Token Error:', error);
            API.logout();
            throw error;
        }
    },

    logout: async () => {
        const accessToken = localStorage.getItem('access_token');
        if (accessToken) {
            try {
                await fetch(`${API_BASE_URL}/logout`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${accessToken}`,
                        'Accept': 'application/json',
                        'X-Agency-ID': localStorage.getItem('agency_id')
                    }
                });
            } catch (e) {
                // Ignore logout errors
            }
        }
        API.clearToken();
        // Navigate to global login - handle both root and subdirectory pages
        const basePath = window.location.pathname.split('/ADVERTISER')[0];
        window.location.href = basePath + '/login.php';
    },

    clearToken: () => {
        localStorage.removeItem('access_token');
        localStorage.removeItem('refresh_token');
        localStorage.removeItem('user');
        localStorage.removeItem('agency_id');
    },

    // --- Data Fetching ---

    fetchWithAuth: async (endpoint, options = {}) => {
        let accessToken = localStorage.getItem('access_token');

        if (!accessToken) {
            API.logout();
            return;
        }

        const url = `${API_BASE_URL}/${endpoint}`;

        // Prepare headers
        const headers = {
            'Accept': 'application/json',
            'Authorization': `Bearer ${accessToken}`,
            'X-Agency-ID': localStorage.getItem('agency_id'),
            ...options.headers
        };

        const config = {
            ...options,
            headers
        };

        try {
            let response = await fetch(url, config);

            // Handle 401 Unauthorized (Token Expired)
            if (response.status === 401) {
                console.log('Access token expired, attempting refresh...');
                try {
                    const newAccessToken = await API.refreshToken();
                    // Retry original request with new token
                    config.headers['Authorization'] = `Bearer ${newAccessToken}`;
                    response = await fetch(url, config);
                } catch (refreshError) {
                    // Refresh failed, user is already logged out by refreshToken()
                    return;
                }
            }

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || `API Error: ${response.status}`);
            }

            return data;
        } catch (error) {
            console.error(`Fetch Error (${endpoint}):`, error);
            throw error;
        }
    },

    // Check if user is logged in
    isAuthenticated: () => {
        return !!localStorage.getItem('access_token');
    }
};
