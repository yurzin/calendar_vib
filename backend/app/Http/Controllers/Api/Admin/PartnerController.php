<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index(): JsonResponse
    {
        $partners = Partner::withTrashed()->get()->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'logo' => $p->logo,
            'site' => $p->url,
            'profile_id' => $p->profile_id,
            'profile'    => $p->profile ? ['id' => $p->profile->id, 'name' => $p->profile->name] : null,
            'checked' => $p->deleted_at === null,
        ]);

        return response()->json(['partners' => $partners]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $logoPath = Partner::uploadImage($request);

        $partner = Partner::create([
            'name' => $validated['name'],
            'url' => $validated['url'],
            'logo' => $logoPath,
            'profile_id' => $request->input('profile_id') ?: null,
        ]);

        return response()->json([
            'id' => $partner->id,
            'name' => $partner->name,
            'logo' => $partner->logo,
            'site' => $partner->url,
            'checked' => true,
        ], 201);
    }

    public function update(Request $request, Partner $partner): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Загружаем новый логотип и удаляем старый (если новый передан)
        $logoPath = Partner::uploadImage($request, $partner->logo);

        $partner->update([
            'name' => $validated['name'],
            'url' => $validated['url'],
            'logo' => $logoPath ?? $partner->logo,
            'profile_id' => $request->input('profile_id') ?: null,
        ]);

        return response()->json([
            'id' => $partner->id,
            'name' => $partner->name,
            'logo' => $partner->logo,
            'site' => $partner->url,
            'checked' => $partner->deleted_at === null,
        ]);
    }

    public function destroy(Partner $partner): JsonResponse
    {
        // SoftDelete — запись остаётся в БД, deleted_at проставляется
        $partner->delete();

        return response()->json(['message' => 'Участник удалён'], 200);
    }

    public function restore(int $id): JsonResponse
    {
        $partner = Partner::withTrashed()->findOrFail($id);
        $partner->restore();

        return response()->json([
            'id'      => $partner->id,
            'name'    => $partner->name,
            'logo'    => $partner->logo,
            'site'    => $partner->url,
            'checked' => true,
        ]);
    }

}
