import axios from 'axios';

export function getErrorMessage(e: unknown, fallback: string): string {
  return (axios.isAxiosError(e) && e.response?.data?.message) || fallback;
}

export function getValidationErrors(e: unknown): Record<string, string[]> | undefined {
  return axios.isAxiosError(e) ? e.response?.data?.errors : undefined;
}
