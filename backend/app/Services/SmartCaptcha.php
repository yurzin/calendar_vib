<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
                ->connectTimeout(2)
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

        // 4xx — ошибка настройки (например, неверный ключ): пропускаем, но это нужно чинить
        if ($response->clientError()) {
            Log::critical('SmartCaptcha отвергла запрос, капча не работает', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return true;
        }

        if (! $response->ok()) {
            report(new \RuntimeException('SmartCaptcha validate: HTTP '.$response->status()));

            return true;
        }

        return $response->json('status') === 'ok';
    }
}
