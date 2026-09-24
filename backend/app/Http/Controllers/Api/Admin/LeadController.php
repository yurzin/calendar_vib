<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;

class LeadController extends Controller
{
    public function index(): JsonResponse
    {
        $leads = Lead::orderByDesc('id')->get()->map(fn(Lead $lead) => [
            'id' => $lead->id,
            'last_name' => $lead->last_name,
            'first_name' => $lead->first_name,
            'middle_name' => $lead->middle_name,
            'birthday' => $lead->birthday?->toDateString(),
            'city' => $lead->city,
            'workplace' => $lead->workplace,
            'position' => $lead->position,
            'email' => $lead->email,
            'phone' => $lead->phone,
            'source' => $lead->source,
            'consent_at' => $lead->consent_at?->toIso8601String(),
            'created_at' => $lead->created_at?->toIso8601String(),
        ]);

        return response()->json(['leads' => $leads]);
    }

    public function destroy(Lead $lead): JsonResponse
    {
        $lead->delete();

        return response()->json(['message' => 'Заявка удалена']);
    }
}
