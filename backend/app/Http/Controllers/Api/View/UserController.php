<?php

namespace App\Http\Controllers\Api\View;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Получить текущего пользователя (или null если не авторизован)
     */
    use ApiResponse;

    public function getUser(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            // Возвращаем null вместо ошибки 401
            return $this->successResponse(null, 'Not authenticated');
        }

        return $this->successResponse([
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames(),
        ]);
    }
}
