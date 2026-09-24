<?php

namespace App\Http\Controllers\Api\View;

use App\Http\Controllers\Controller;
use App\Mail\NewLead;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Throwable;

class LeadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'birthday' => 'required|date|before:today|after:1900-01-01',
            'city' => 'required|string|max:255',
            'workplace' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => ['required', 'string', 'max:32', 'regex:/^[\d\s()+\-]{6,}$/'],
            'source' => 'nullable|string|max:255',
            'consent' => 'accepted',
        ], [
            'last_name.required' => 'Укажите фамилию.',
            'first_name.required' => 'Укажите имя.',
            'birthday.required' => 'Укажите дату рождения.',
            'birthday.date' => 'Укажите корректную дату рождения.',
            'birthday.before' => 'Укажите корректную дату рождения.',
            'birthday.after' => 'Укажите корректную дату рождения.',
            'city.required' => 'Укажите город.',
            'workplace.required' => 'Укажите место работы.',
            'position.required' => 'Укажите должность.',
            'email.required' => 'Укажите e-mail.',
            'email.email' => 'Укажите корректный e-mail.',
            'phone.required' => 'Укажите телефон.',
            'phone.regex' => 'Укажите корректный номер телефона.',
            'consent.accepted' => 'Необходимо согласие на обработку персональных данных.',
            '*.max' => 'Слишком длинное значение.',
        ]);

        $lead = Lead::create([
            ...Arr::except($validated, 'consent'),
            'ip' => $request->ip(),
            'consent_at' => now(),
        ]);

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
