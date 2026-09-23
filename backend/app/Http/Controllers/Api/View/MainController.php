<?php

namespace App\Http\Controllers\Api\View;

use App\Models\ArchiveIssue;
use App\Models\Partner;
use App\Models\SliderImage;

class MainController
{
    public function index()
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

    public function members()
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

    public function archive()
    {
        $issues = ArchiveIssue::orderByDesc('year')->get()->map(fn($i) => [
            'year' => $i->year,
            'cover' => $i->cover_path,
            'pdfUrl' => $i->pdf_path,
            'pageUrl' => $i->page_url,
        ]);

        return response()->json(['issues' => $issues]);
    }

    public function slider()
    {
        $images = SliderImage::orderBy('sort_order')->orderBy('id')->get()->map(fn($i) => [
            'src' => $i->image_path,
            'label' => $i->label,
        ]);

        return response()->json(['images' => $images]);
    }

}
