<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SliderImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SliderController extends Controller
{
    private function present(SliderImage $image): array
    {
        return [
            'id' => $image->id,
            'image' => $image->image_path,
            'label' => $image->label,
            'sort_order' => $image->sort_order,
        ];
    }

    public function index(): JsonResponse
    {
        $images = SliderImage::orderBy('sort_order')->orderBy('id')->get()->map(fn($i) => $this->present($i));

        return response()->json(['images' => $images]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:8192',
            'label' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $nextOrder = $validated['sort_order'] ?? ((int) SliderImage::max('sort_order') + 1);

        $image = SliderImage::create([
            'image_path' => SliderImage::uploadImage($request),
            'label' => $validated['label'] ?? null,
            'sort_order' => $nextOrder,
        ]);

        return response()->json($this->present($image), 201);
    }

    public function update(Request $request, SliderImage $slider): JsonResponse
    {
        $validated = $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'label' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $imagePath = SliderImage::uploadImage($request, $slider->image_path);

        $slider->update([
            'image_path' => $imagePath ?? $slider->image_path,
            'label' => $validated['label'] ?? null,
            'sort_order' => $validated['sort_order'] ?? $slider->sort_order,
        ]);

        return response()->json($this->present($slider));
    }

    public function destroy(SliderImage $slider): JsonResponse
    {
        if ($slider->image_path) {
            Storage::disk('public')->delete(Str::after($slider->image_path, '/storage/'));
        }

        $slider->delete();

        return response()->json(['message' => 'Изображение удалено']);
    }
}
