<?php

namespace App\Http\Controllers\Api\View;

use App\Models\ArchiveIssue;
use App\Models\Participation;
use App\Models\Partner;
use App\Models\SliderImage;
use Illuminate\Http\JsonResponse;

class MainController
{
    public function index(): JsonResponse
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

    public function members(): JsonResponse
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

    public function participation(string $slug): JsonResponse
    {
        $participation = Participation::query()
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'participation' => [
                'id'          => $participation->id,
                'name'        => $participation->name,
                'slug'        => $participation->slug,
                'description' => $participation->description,
                'price'       => $participation->price,
                'sold_out'    => (bool) $participation->sold_out,
            ],
        ]);
    }

    public function participations(): JsonResponse
    {
        $participations = Participation::all()->sortBy('id')->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'slug' => $p->slug,
            'price' => $p->price, //rtrim(rtrim($p->price, '0'), '.'),
            'description' => $p->description,
            'sold_out' => (bool)$p->sold_out,
        ])->values();

        return response()->json([
            'participations' => $participations
        ]);
    }

    public function archive(): JsonResponse
    {
        $issues = ArchiveIssue::orderByDesc('year')->get()->map(fn($i) => [
            'year' => $i->year,
            'cover' => $i->cover_path,
            'pdfUrl' => $i->pdf_path,
            'pageUrl' => $i->page_url,
        ]);

        return response()->json(['issues' => $issues]);
    }

    public function slider(): JsonResponse
    {
        $images = SliderImage::orderBy('sort_order')->orderBy('id')->get()->map(fn($i) => [
            'src' => $i->image_path,
            'label' => $i->label,
        ]);

        return response()->json(['images' => $images]);
    }

}
