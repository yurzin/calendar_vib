<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    // Поиск профилей для select
    public function search(Request $request): JsonResponse
    {
        $profiles = Profile::query()
            ->when($request->q, fn($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->limit(20)
            ->get(['id', 'name']);

        return response()->json(['profiles' => $profiles]);
    }

    // Создание нового профиля на лету
    public function store(Request $request): JsonResponse
    {
        $request->validate(['name' => 'required|string|max:255']);

        $profile = Profile::create(['name' => $request->name]);

        return response()->json(['id' => $profile->id, 'name' => $profile->name], 201);
    }
}
