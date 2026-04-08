<?php

namespace App\Http\Controllers\Api\View;

use App\Models\Partner;

class MainController
{
    public function index()
    {
        $partners = Partner::where('is_paid', true)->get()->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'logo' => $p->logo,
            'site' => $p->url,
            'is_paid' => (bool)$p->is_paid,
            'role' => $p->profile?->name,
            'persons' => $p->persons
        ]);

        return response()->json([
            'partners' => $partners,
            'user' => auth()->user(),
        ]);
    }

    public function members()
    {
        $partners = Partner::all()->sortByDesc('is_paid')->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'logo' => $p->logo,
            'site' => $p->url,
            'is_paid' => (bool)$p->is_paid,
            'role' => $p->profile?->name,
            'persons' => $p->persons
        ])->values();

        return response()->json([
            'partners' => $partners,
            'user' => auth()->user(),
        ]);
    }

}
