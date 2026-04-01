import { ref } from 'vue';
import axios from '../utils/axios';

const user = ref(null);
const loading = ref(false);
const errors = ref({});

export function useAuth() {
  const checkAuth = async () => {
    loading.value = true;
    errors.value = {};

    try {
      // Получаем CSRF cookie
      await axios.get('/sanctum/csrf-cookie');

      // Получаем пользователя
      const response = await axios.get('/api/user');

      // Обрабатываем ответ в зависимости от структуры
      user.value = response.data.data || response.data;
      return user.value;
    } catch (error) {
      console.error('Auth check failed:', error);
      user.value = null;
      return null;
    } finally {
      loading.value = false;
    }
  };

  const login = async (credentials) => {
    loading.value = true;
    errors.value = {};

    try {
      await axios.get('/sanctum/csrf-cookie');
      const response = await axios.post('/api/login', credentials);

      console.log('Login response:', response.data);
      user.value = response.data.data || response.data;
      console.log(user.value);

      return response.data;
    } catch (error) {
      console.error('Login error:', error);

      if (error.response?.status === 422) {
        errors.value = error.response.data.errors;
      } else {
        errors.value = {
          message: error.response?.data?.message || 'Login failed. Please try again.'
        };
      }
      throw error;
    } finally {
      loading.value = false;
    }
  };

  const logout = async () => {
    loading.value = true;

    try {
      await axios.post('/api/logout');
      user.value = null;
      return true;
    } catch (error) {
      console.error('Logout failed:', error);
      throw error;
    } finally {
      loading.value = false;
    }
  };

  const register = async (data) => {
    loading.value = true;
    errors.value = {};

    try {
      await axios.get('/sanctum/csrf-cookie');
      const response = await axios.post('/api/register', data);

      user.value = response.data.user || response.data;

      return response.data;
    } catch (error) {
      console.error('Registration error:', error);

      if (error.response?.status === 422) {
        errors.value = error.response.data.errors;
      } else {
        errors.value = {
          message: error.response?.data?.message || 'Registration failed. Please try again.'
        };
      }
      throw error;
    } finally {
      loading.value = false;
    }
  };

  return {
    user,
    loading,
    errors,
    checkAuth,
    login,
    logout,
    register
  };
}
