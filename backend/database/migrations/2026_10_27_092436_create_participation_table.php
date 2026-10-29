<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('participation', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Название тарифа/участия
            $table->decimal('price', 8, 2); // Цена (оптимально для денег, например: 999999.99)
            $table->boolean('sold_out')->default(false); // Флаг "распродано" (true/false)
            $table->timestamps(); // Создает created_at и updated_at
            $table->softDeletes(); // Создает deleted_at для мягкого удаления
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participation');
    }
};
