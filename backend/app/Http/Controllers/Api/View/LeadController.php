<?php

namespace App\Http\Controllers\Api\View;

use App\Http\Controllers\Controller;
use App\Mail\NewLead;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Throwable;

class LeadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'phone' => ['required', 'string', 'max:32', 'regex:/^[\d\s()+\-]{6,}$/'],
            'source' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Укажите имя и фамилию.',
            'company.required' => 'Укажите компанию или организацию.',
            'phone.required' => 'Укажите телефон.',
            'phone.regex' => 'Укажите корректный номер телефона.',
            '*.max' => 'Слишком длинное значение.',
        ]);

        $lead = Lead::create([...$validated, 'ip' => $request->ip()]);

        // Заявка уже сохранена — сбой почты не должен ломать ответ пользователю
        $recipient = config('services.leads.email');
        if ($recipient) {
            try {
                Mail::to($recipient)->send(new NewLead($lead));
            } catch (Throwable $e) {
                report($e);
            }
        }

        return response()->json(['message' => 'Заявка отправлена'], 201);
    }
}
