<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class SliderImage extends Model
{
    use HasFactory;

    protected $fillable = ['image_path', 'label', 'sort_order'];

    public static function storeFile(UploadedFile $file): string
    {
        $path = 'uploads/slider/' . Str::random(20) . '.jpg';

        $manager = new ImageManager(new Driver());
        $encoded = $manager->read($file->getRealPath())->toJpeg(85);

        Storage::disk('public')->put($path, $encoded);

        return '/storage/' . $path;
    }

    public static function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete(Str::after($path, '/storage/'));
        }
    }
}
