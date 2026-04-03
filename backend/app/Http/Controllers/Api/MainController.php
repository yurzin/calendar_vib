<?php

namespace App\Http\Controllers\Api;

class MainController
{
    public function index()
    {
        return response()->json([
            'message' => 'Основной сайт',
            'user' => auth()->user(),
        ]);
    }

}
