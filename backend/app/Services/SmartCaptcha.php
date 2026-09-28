<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Throwable;

// Проверка токена Yandex SmartCaptcha: https://yandex.cloud/ru/docs/smartcaptcha/concepts/validation
class SmartCaptcha
{
    public static function enabled(): bool
    {
        return (bool) config('services.smartcaptcha.server_key');
    }

    public static function passes(string $token, ?string $ip): bool
    {
        try {
            $response = Http::asForm()
                ->timeout(3)
                ->post('https://smartcaptcha.yandexcloud.net/validate', [
                    'secret' => config('services.smartcaptcha.server_key'),
                    'token' => $token,
                    'ip' => $ip,
                ]);
        } catch (Throwable $e) {
            report($e);

            // Яндекс рекомендует пропускать пользователя, если сервис проверки недоступен
            return true;
        }

        if (! $response->ok()) {
            report(new \RuntimeException('SmartCaptcha validate: HTTP ' . $response->status()));

            return true;
        }

        return $response->json('status') === 'ok';
    }
}
