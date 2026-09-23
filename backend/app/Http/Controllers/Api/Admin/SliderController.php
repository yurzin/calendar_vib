<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SliderImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $request->validate([
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:8192',
        ]);

        $nextOrder = (int) SliderImage::max('sort_order');
        $created = [];

        foreach ($request->file('images') as $file) {
            $nextOrder++;
            $created[] = SliderImage::create([
                'image_path' => SliderImage::storeFile($file),
                'sort_order' => $nextOrder,
            ]);
        }

        return response()->json([
            'images' => collect($created)->map(fn($i) => $this->present($i)),
        ], 201);
    }

    public function update(Request $request, SliderImage $slider): JsonResponse
    {
        $validated = $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'label' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            SliderImage::deleteFile($slider->image_path);
            $imagePath = SliderImage::storeFile($request->file('image'));
        }

        $slider->update([
            'image_path' => $imagePath ?? $slider->image_path,
            'label' => $validated['label'] ?? null,
            'sort_order' => $validated['sort_order'] ?? $slider->sort_order,
        ]);

        return response()->json($this->present($slider));
    }

    public function destroy(SliderImage $slider): JsonResponse
    {
        SliderImage::deleteFile($slider->image_path);
        $slider->delete();

        return response()->json(['message' => 'Изображение удалено']);
    }
}
