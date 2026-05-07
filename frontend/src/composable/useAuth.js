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
      const response = await axios.get('/api/user');
      user.value = response.data?.success && response.data?.data
        ? response.data.data
        : null;
      return user.value;
    } catch (error) {
      // 401 — нормально, пользователь не авторизован
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

      user.value = response.data.data || null;

      const allowedRoles = ['admin', 'editor'];
      const hasAccess = user.value?.roles?.some(role => allowedRoles.includes(role));

      if (!hasAccess) {
        user.value = null;
        window.location.href = import.meta.env.VITE_MAIN_URL ?? 'http://calendar.local';
        return; // ← undefined, компонент увидит !result и остановится
      }

      return response.data; // ← только для admin/editor
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
    console.log(loading)
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

      user.value = response.data.data || null;

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
