<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class Partner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['logo', 'name', 'url', 'profile_id'];

    public static function uploadImage(Request $request, ?string $oldPath = null): ?string
    {
        if (!$request->hasFile('logo')) {
            return null;
        }

        if ($oldPath) {
            $storagePath = Str::after($oldPath, '/storage/');
            Storage::disk('public')->delete($storagePath);
        }

        $logo = $request->file('logo');
        $slug = Str::slug($request->name);
        $path = "uploads/partners/{$slug}.jpg";

        // Intervention Image 3.x API
        $manager = new ImageManager(new Driver());
        $image   = $manager->read($logo->getRealPath())
            ->toJpeg(85);

        Storage::disk('public')->put($path, $image);

        return '/storage/' . $path;
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
