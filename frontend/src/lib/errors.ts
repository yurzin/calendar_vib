import axios from 'axios';

export function getErrorMessage(e: unknown, fallback: string): string {
  if (!axios.isAxiosError(e)) return fallback;
  if ((e.response?.status ?? 0) >= 500) return fallback;
  const message = e.response?.data?.message;
  return typeof message === 'string' && message.trim() !== '' ? message : fallback;
}

export function getValidationErrors(e: unknown): Record<string, string[]> | undefined {
  if (!axios.isAxiosError(e) || e.response?.status !== 422) return undefined;
  const errors: unknown = e.response.data?.errors;
  if (typeof errors !== 'object' || errors === null || Array.isArray(errors)) return undefined;
  return Object.fromEntries(
    Object.entries(errors).filter((entry): entry is [string, string[]] =>
      Array.isArray(entry[1]) && entry[1].length > 0),
  );
}
