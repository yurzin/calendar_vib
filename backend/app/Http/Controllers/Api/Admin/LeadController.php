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
            'name' => $lead->name,
            'company' => $lead->company,
            'phone' => $lead->phone,
            'source' => $lead->source,
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
