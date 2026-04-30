<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CalendarExportController extends Controller
{
    /**
     * Месяцы для отображения в UI
     */
    public static array $months = [
        1  => 'Январь',
        2  => 'Февраль',
        3  => 'Март',
        4  => 'Апрель',
        5  => 'Май',
        6  => 'Июнь',
        7  => 'Июль',
        8  => 'Август',
        9  => 'Сентябрь',
        10 => 'Октябрь',
        11 => 'Ноябрь',
        12 => 'Декабрь',
    ];

    // ─────────────────────────────────────────────────────────────────────
    // GET /api/export/calendar/stats
    // Возвращает количество персон с ДР по каждому месяцу — для UI кнопок
    // ─────────────────────────────────────────────────────────────────────
    public function stats(): JsonResponse
    {
        // Считаем активных персон с заполненным birth_month и birth_day
        $counts = Person::whereNull('deleted_at')
            ->whereNotNull('birth_month')
            ->whereNotNull('birth_day')
            ->selectRaw('birth_month, COUNT(*) as total')
            ->groupBy('birth_month')
            ->orderBy('birth_month')
            ->pluck('total', 'birth_month');

        $result = [];
        foreach (self::$months as $num => $name) {
            $result[] = [
                'month'      => $num,
                'name'       => $name,
                'short_name' => mb_substr($name, 0, 3),
                'count'      => $counts[$num] ?? 0,
            ];
        }

        return response()->json(['months' => $result]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // GET /api/export/calendar/json?month=4
    // GET /api/export/calendar/json        (все месяцы)
    // Возвращает JSON-структуру по дням для InDesign-скрипта
    // ─────────────────────────────────────────────────────────────────────
    public function exportJson(Request $request): JsonResponse
    {
        $month = $request->integer('month', 0); // 0 = все месяцы

        $query = Person::whereNull('deleted_at')
            ->whereNotNull('birth_month')
            ->whereNotNull('birth_day')
            ->with('partner')
            ->orderBy('birth_month')
            ->orderBy('birth_day')
            ->orderBy('last_name');

        if ($month >= 1 && $month <= 12) {
            $query->where('birth_month', $month);
        }

        $persons = $query->get();

        // Группируем по "месяц-день"
        $grouped = [];

        foreach ($persons as $p) {
            $key = sprintf('%02d-%02d', $p->birth_month, $p->birth_day);

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'month'     => (int) $p->birth_month,
                    'day'       => (int) $p->birth_day,
                    'month_name'=> self::$months[$p->birth_month] ?? '',
                    'date_label'=> $p->birth_day . ' ' . (self::$months[$p->birth_month] ?? ''),
                    'persons'   => [],
                ];
            }

            $grouped[$key]['persons'][] = $this->formatPerson($p);
        }

        // Сортируем ключи
        ksort($grouped);

        return response()->json([
            'month'      => $month ?: 'all',
            'month_name' => $month ? (self::$months[$month] ?? '') : 'Все месяцы',
            'total'      => $persons->count(),
            'days'       => array_values($grouped),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // GET /api/export/calendar/download?month=4
    // Скачивает JSON-файл (для передачи в InDesign-скрипт)
    // ─────────────────────────────────────────────────────────────────────
    public function downloadJson(Request $request): Response
    {
        $month = $request->integer('month', 0);

        // Переиспользуем логику exportJson
        $jsonResponse = $this->exportJson($request);
        $data         = $jsonResponse->getData(true);

        $filename = $month
            ? 'calendar_' . $month . '_' . (self::$months[$month] ?? 'month') . '.json'
            : 'calendar_all.json';

        // Транслитерируем имя файла (кириллица в заголовке Content-Disposition)
        $filename = $this->transliterate($filename);

        return response(
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            200,
            [
                'Content-Type'        => 'application/json; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }

    // ─────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────

    private function formatPerson(Person $p): array
    {
        return [
            'id'             => $p->id,
            'last_name'      => $p->last_name,
            'first_name'     => $p->first_name,
            'middle_name'    => $p->middle_name,
            'full_name'      => trim("{$p->last_name} {$p->first_name} " . ($p->middle_name ?? '')),
            'position_short' => $p->position_short,
            'position_full'  => $p->position_full,
            'company'        => $p->partner?->name ?? '',
        ];
    }

    private function transliterate(string $str): string
    {
        $map = [
            'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'yo',
            'ж'=>'zh','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m',
            'н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u',
            'ф'=>'f','х'=>'h','ц'=>'ts','ч'=>'ch','ш'=>'sh','щ'=>'sch',
            'ъ'=>'','ы'=>'y','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya',
            ' '=>'_',
        ];
        return strtr(mb_strtolower($str), $map);
    }
}
