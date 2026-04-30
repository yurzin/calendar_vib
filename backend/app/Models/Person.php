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

class Person extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'persons';

    protected $fillable = [
        'partner_id',
        'last_name',
        'first_name',
        'middle_name',
        'short_name',
        'phone',
        'email',
        'web',
        'position_short',
        'position_full',
        'photo_path',
        'photo_thumb_path',
        'birth_day',
        'birth_month',
    ];


    // Аксессор для полного имени (на случай, если понадобится получить из модели)
    public function getFullNameAttribute(): string
    {
        $parts = [$this->last_name, $this->first_name];
        if ($this->middle_name) {
            $parts[] = $this->middle_name;
        }
        return implode(' ', $parts);
    }

    // Аксессор для короткого имени
    public function getShortNameAttribute(): string
    {
        $short = $this->last_name . ' ' . mb_substr($this->first_name, 0, 1) . '.';
        if ($this->middle_name) {
            $short .= mb_substr($this->middle_name, 0, 1) . '.';
        }
        return $short;
    }

    public static function uploadImage(Request $request, ?string $oldPath = null): ?array
    {
        if (!$request->hasFile('photo')) {
            return null;
        }
        $maxSize   = 5 * 1024 * 1024; // 5MB

        // Удаляем старое фото
        if ($oldPath) {
            $storagePath = Str::after($oldPath, '/storage/');
            Storage::disk('public')->delete($storagePath);
        }

        $file = $request->file('photo');
        $slug = Str::slug(($request->last_name) . '_' . ($request->first_name) . '_' . ($request->middle_name));
        $path      = "uploads/persons/{$slug}.jpg";
        $thumbPath = "uploads/persons/thumb/{$slug}_thumb.jpg";

        $manager = new ImageManager(new Driver());

        // Исправлено: используем getSize() для получения размера файла
        if ($file->getSize() > $maxSize) {
            $quality  = ($maxSize / $file->getSize()) * 100;
            $image = $manager->read($file->getRealPath())
                ->toJpeg((int)$quality);
        } else {
            $image = $manager->read($file->getRealPath())
                ->toJpeg(100);
        }

        Storage::disk('public')->put($path, $image);

        // Thumbnail 150×150
        $thumb = $manager->read($file->getRealPath())
            ->cover(150, 150)
            ->toJpeg(80);
        Storage::disk('public')->put($thumbPath, $thumb);

        return [
            'photo' => '/storage/' . $path,
            'thumb' => '/storage/' . $thumbPath,
        ];
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    // Скоуп для активных записей (не удаленных)
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    // Скоуп для именинников в текущем месяце
    public function scopeBirthdayThisMonth($query)
    {
        return $query->whereMonth('birthday', now()->month);
    }

    // Скоуп для именинников сегодня
    public function scopeBirthdayToday($query)
    {
        return $query->whereMonth('birthday', now()->month)
            ->whereDay('birthday', now()->day);
    }
}
