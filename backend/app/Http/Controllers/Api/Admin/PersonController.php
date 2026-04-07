<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PersonController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Person::withTrashed()->with('partner');

        // Фильтр по партнеру
        if ($request->has('partner_id')) {
            $query->where('partner_id', $request->partner_id);
        }

        // Фильтр по месяцу рождения
        if ($request->has('birth_month')) {
            $query->where('birth_month', $request->birth_month);
        }

        // Поиск по ФИО
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('last_name', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%");
            });
        }

        $persons = $query->get()->map(fn($p) => [
            'id' => $p->id,
            'partner_id' => $p->partner_id,
            'partner' => $p->partner ? [
                'id' => $p->partner->id,
                'name' => $p->partner->name
            ] : null,
            'last_name' => $p->last_name,
            'first_name' => $p->first_name,
            'middle_name' => $p->middle_name,
            'short_name' => $p->short_name,
            'birth_month' => $p->birth_month,
            'birth_day' => $p->birth_day,
            'position_short' => $p->position_short,
            'position_full' => $p->position_full,
            'photo_path' => $p->photo_path,
            'photo_thumb_path' => $p->photo_thumb_path,
            'phone' => $p->phone,
            'email' => $p->email,
            'site' => $p->site,
            'checked' => $p->deleted_at === null,
        ]);

        return response()->json(['persons' => $persons]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'partner_id'     => 'nullable|integer|exists:partners,id',
            'last_name'      => 'required|string|max:80',
            'first_name'     => 'required|string|max:80',
            'middle_name'    => 'nullable|string|max:80',
            'phone'          => 'nullable|string|max:80',
            'email'          => 'nullable|email|max:80',
            'site'           => 'nullable|string|max:80',
            'position_short' => 'nullable|string|max:255',
            'position_full'  => 'nullable|string|max:500',
            'birth_day'      => 'nullable|integer|min:1|max:31',
            'birth_month'    => 'nullable|integer|min:1|max:12',
            'photo'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10024',
        ]);

        if ($request->hasFile('photo')) {
            $photoData = Person::uploadImage($request);
            $validated['photo_path']       = $photoData['photo'];
            $validated['photo_thumb_path'] = $photoData['thumb'];
        }

        $shortName = $validated['last_name'] . ' ' . mb_substr($validated['first_name'], 0, 1) . '.';
        if (!empty($validated['middle_name'])) {
            $shortName .= mb_substr($validated['middle_name'], 0, 1) . '.';
        }
        $validated['short_name'] = $shortName;

        $person = Person::create($validated);
        $person->load('partner');

        return response()->json([
            'id'             => $person->id,
            'partner_id'     => $person->partner_id,
            'partner'        => $person->partner ? [
                'id'   => $person->partner->id,
                'name' => $person->partner->name,
            ] : null,
            'last_name'      => $person->last_name,
            'first_name'     => $person->first_name,
            'middle_name'    => $person->middle_name,
            'short_name'     => $person->short_name,
            'birth_day'      => $person->birth_day,
            'birth_month'    => $person->birth_month,
            'position_short' => $person->position_short,
            'position_full'  => $person->position_full,
            'photo_path'     => $person->photo_path,
            'photo_thumb_path' => $person->photo_thumb_path,
            'phone'          => $person->phone,
            'email'          => $person->email,
            'site'           => $person->site,
            'checked'        => true,
        ], 201);
    }

    public function show(Person $person): JsonResponse
    {
        return response()->json([
            'id' => $person->id,
            'partner_id' => $person->partner_id,
            'partner' => $person->partner ? [
                'id' => $person->partner->id,
                'name' => $person->partner->name
            ] : null,
            'last_name' => $person->last_name,
            'first_name' => $person->first_name,
            'middle_name' => $person->middle_name,
            'short_name' => $person->short_name,
            'position_short' => $person->position_short,
            'position_full' => $person->position_full,
            'photo_path' => $person->photo_path,
            'photo_thumb_path' => $person->photo_thumb_path,
            'birth_day' => $person->birth_day,
            'birth_month' => $person->birth_month,
            'created_at' => $person->created_at,
            'updated_at' => $person->updated_at,
        ]);
    }

    public function update(Request $request, Person $person): JsonResponse
    {
        $validated = $request->validate([
            'partner_id'     => 'nullable|integer|exists:partners,id',
            'last_name'      => 'required|string|max:80',
            'first_name'     => 'required|string|max:80',
            'middle_name'    => 'nullable|string|max:80',
            'phone'          => 'nullable|string|max:80',
            'email'          => 'nullable|email|max:80',
            'site'           => 'nullable|string|max:80',
            'position_short' => 'nullable|string|max:255',
            'position_full'  => 'nullable|string|max:500',
            'birth_day'      => 'nullable|integer|min:1|max:31',
            'birth_month'    => 'nullable|integer|min:1|max:12',
            'photo'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10024',
        ]);

        if ($request->hasFile('photo')) {
            $photoData = Person::uploadImage($request, $person->photo_path);
            $validated['photo_path']       = $photoData['photo'];
            $validated['photo_thumb_path'] = $photoData['thumb'];
        } elseif ($request->input('remove_photo')) {
            // Удаление фото
            $validated['photo_path']       = null;
            $validated['photo_thumb_path'] = null;
        }

        // Пересчитываем short_name
        $lastName   = $validated['last_name']  ?? $person->last_name;
        $firstName  = $validated['first_name'] ?? $person->first_name;
        $middleName = $validated['middle_name'] ?? $person->middle_name;
        $shortName  = $lastName . ' ' . mb_substr($firstName, 0, 1) . '.';
        if (!empty($middleName)) {
            $shortName .= mb_substr($middleName, 0, 1) . '.';
        }
        $validated['short_name'] = $shortName;

        $person->update($validated);
        $person->load('partner');

        return response()->json([
            'id'             => $person->id,
            'partner_id'     => $person->partner_id,
            'partner'        => $person->partner ? [
                'id'   => $person->partner->id,
                'name' => $person->partner->name,
            ] : null,
            'last_name'      => $person->last_name,
            'first_name'     => $person->first_name,
            'middle_name'    => $person->middle_name,
            'short_name'     => $person->short_name,
            'birth_day'      => $person->birth_day,
            'birth_month'    => $person->birth_month,
            'position_short' => $person->position_short,
            'position_full'  => $person->position_full,
            'photo_path'     => $person->photo_path,
            'photo_thumb_path' => $person->photo_thumb_path,
            'phone'          => $person->phone,
            'email'          => $person->email,
            'site'           => $person->site,
            'checked'        => $person->deleted_at === null,
        ]);
    }

    public function destroy(Person $person): JsonResponse
    {
        $person->delete();

        return response()->json(['message' => 'Участник удален'], 200);
    }

    public function restore(int $id): JsonResponse
    {
        $person = Person::withTrashed()->findOrFail($id);
        $person->restore();

        return response()->json([
            'id' => $person->id,
            'full_name' => $person->full_name,
            'checked' => true,
        ]);
    }

    // Дополнительный метод для получения именинников
    public function birthdays(Request $request): JsonResponse
    {
        $month = $request->get('month', now()->month);

        $persons = Person::active()
            ->where('birth_month', $month)
            ->orderBy('birth_day')
            ->with('partner')
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'full_name' => $p->full_name,
                'short_name' => $p->short_name,
                'birthday' => $p->birthday->format('Y-m-d'),
                'birth_day' => $p->birth_day,
                'position_short' => $p->position_short,
                'photo_thumb_path' => $p->photo_thumb_path,
                'partner_name' => $p->partner?->name,
            ]);

        return response()->json(['birthdays' => $persons]);
    }
}
