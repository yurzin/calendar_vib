<?php

namespace App\Http\Controllers\Api\View;

use App\Models\Partner;

class MainController
{
    public function index()
    {
        $partners = Partner::all()->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'logo' => $p->logo,
            'site' => $p->url,
            'role' => $p->profile?->name,
            'persons' => $p->persons
        ]);

        return response()->json([
            'partners' => $partners,
            'user' => auth()->user(),
        ]);
    }

}
