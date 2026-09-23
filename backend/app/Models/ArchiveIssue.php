<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ArchiveIssue extends Model
{
    use HasFactory;

    protected $fillable = ['year', 'cover_path', 'pdf_path', 'pdf_original_name', 'page_url'];

    public static function uploadCover(Request $request, ?string $oldPath = null): ?string
    {
        if (!$request->hasFile('cover')) {
            return null;
        }

        if ($oldPath) {
            Storage::disk('public')->delete(Str::after($oldPath, '/storage/'));
        }

        $cover = $request->file('cover');
        $path = "uploads/archive/{$request->input('year')}-cover.jpg";

        $manager = new ImageManager(new Driver());
        $image = $manager->read($cover->getRealPath())->toJpeg(85);

        Storage::disk('public')->put($path, $image);

        return '/storage/' . $path;
    }

    public static function uploadPdf(Request $request, ?string $oldPath = null): ?string
    {
        if (!$request->hasFile('pdf')) {
            return null;
        }

        if ($oldPath) {
            Storage::disk('public')->delete(Str::after($oldPath, '/storage/'));
        }

        $pdf = $request->file('pdf');
        $path = "uploads/archive/{$request->input('year')}.pdf";

        Storage::disk('public')->putFileAs('uploads/archive', $pdf, "{$request->input('year')}.pdf");

        return '/storage/' . $path;
    }
}
