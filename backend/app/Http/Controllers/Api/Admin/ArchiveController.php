<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArchiveIssue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ArchiveController extends Controller
{
    private function present(ArchiveIssue $issue): array
    {
        return [
            'id' => $issue->id,
            'year' => $issue->year,
            'cover' => $issue->cover_path,
            'pdf_url' => $issue->pdf_path,
            'pdf_name' => $issue->pdf_original_name,
            'page_url' => $issue->page_url,
        ];
    }

    public function index(): JsonResponse
    {
        $issues = ArchiveIssue::orderByDesc('year')->get()->map(fn($i) => $this->present($i));

        return response()->json(['issues' => $issues]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2000|max:2100|unique:archive_issues,year',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'pdf' => 'nullable|file|mimes:pdf|max:51200',
            'page_url' => 'nullable|url|max:255',
        ]);

        $issue = ArchiveIssue::create([
            'year' => $validated['year'],
            'page_url' => $validated['page_url'] ?? null,
            'cover_path' => ArchiveIssue::uploadCover($request),
            'pdf_path' => ArchiveIssue::uploadPdf($request),
            'pdf_original_name' => $request->hasFile('pdf') ? $request->file('pdf')->getClientOriginalName() : null,
        ]);

        return response()->json($this->present($issue), 201);
    }

    public function update(Request $request, ArchiveIssue $archive): JsonResponse
    {
        $validated = $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100', Rule::unique('archive_issues', 'year')->ignore($archive->id)],
            'cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'pdf' => 'nullable|file|mimes:pdf|max:51200',
            'page_url' => 'nullable|url|max:255',
        ]);

        $coverPath = ArchiveIssue::uploadCover($request, $archive->cover_path);
        $pdfPath = ArchiveIssue::uploadPdf($request, $archive->pdf_path);

        $archive->update([
            'year' => $validated['year'],
            'page_url' => $validated['page_url'] ?? null,
            'cover_path' => $coverPath ?? $archive->cover_path,
            'pdf_path' => $pdfPath ?? $archive->pdf_path,
            'pdf_original_name' => $request->hasFile('pdf')
                ? $request->file('pdf')->getClientOriginalName()
                : $archive->pdf_original_name,
        ]);

        return response()->json($this->present($archive));
    }

    public function destroy(ArchiveIssue $archive): JsonResponse
    {
        if ($archive->cover_path) {
            Storage::disk('public')->delete(Str::after($archive->cover_path, '/storage/'));
        }
        if ($archive->pdf_path) {
            Storage::disk('public')->delete(Str::after($archive->pdf_path, '/storage/'));
        }

        $archive->delete();

        return response()->json(['message' => 'Выпуск удалён']);
    }
}
