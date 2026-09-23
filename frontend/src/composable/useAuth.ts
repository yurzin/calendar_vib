import { ref } from 'vue';
import axios from '../utils/axios';
import { getErrorMessage, getValidationErrors } from '@/lib/errors';
import { getMainSiteUrl } from '@/utils/site';

export interface User {
  id: number;
  name: string;
  email: string;
  roles: string[];
}

export interface AuthErrors {
  message?: string;
  [field: string]: string | string[] | undefined;
}

interface Credentials {
  email: string;
  password: string;
  remember?: boolean;
}

const user = ref<User | null>(null);
const loading = ref(false);
const errors = ref<AuthErrors>({});

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
    } catch {
      // 401 — нормально, пользователь не авторизован
      user.value = null;
      return null;
    } finally {
      loading.value = false;
    }
  };

  const login = async (credentials: Credentials) => {
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
        window.location.href = getMainSiteUrl();
        return; // ← undefined, компонент увидит !result и остановится
      }

      return response.data; // ← только для admin/editor
    } catch (e) {
      console.error('Login error:', e);

      const validationErrors = getValidationErrors(e);
      errors.value = validationErrors ?? {
        message: getErrorMessage(e, 'Login failed. Please try again.')
      };
      throw e;
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
    } catch (e) {
      console.error('Logout failed:', e);
      throw e;
    } finally {
      loading.value = false;
    }
  };

  const register = async (data: Record<string, unknown>) => {
    loading.value = true;
    errors.value = {};

    try {
      await axios.get('/sanctum/csrf-cookie');
      const response = await axios.post('/api/register', data);

      user.value = response.data.data || null;

      return response.data;
    } catch (e) {
      console.error('Registration error:', e);

      const validationErrors = getValidationErrors(e);
      errors.value = validationErrors ?? {
        message: getErrorMessage(e, 'Registration failed. Please try again.')
      };
      throw e;
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
