<?php

namespace App\Http\Controllers\Api\Admin;

class AdminController
{
    public function index()
    {
        return response()->json([
            'message' => 'Admin panel data',
            'user' => auth()->user(),
        ]);    }
}
