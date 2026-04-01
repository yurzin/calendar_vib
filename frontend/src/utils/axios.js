import axios from 'axios';

const axiosInstance = axios.create({
  baseURL: '/',  // относительный путь — всё идёт через Nginx
  withCredentials: true,
  headers: {
    'X-Requested-With': 'XMLHttpRequest',
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
});

export default axiosInstance;
