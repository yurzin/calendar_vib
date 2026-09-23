<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class SliderImage extends Model
{
    use HasFactory;

    protected $fillable = ['image_path', 'label', 'sort_order'];

    public static function uploadImage(Request $request, ?string $oldPath = null): ?string
    {
        if (!$request->hasFile('image')) {
            return null;
        }

        if ($oldPath) {
            Storage::disk('public')->delete(Str::after($oldPath, '/storage/'));
        }

        $image = $request->file('image');
        $path = 'uploads/slider/' . Str::random(20) . '.jpg';

        $manager = new ImageManager(new Driver());
        $encoded = $manager->read($image->getRealPath())->toJpeg(85);

        Storage::disk('public')->put($path, $encoded);

        return '/storage/' . $path;
    }
}
