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
        // Старые заявки сохраняем: «Имя и фамилия» → Фамилия, «Компания» → Место работы
        Schema::table('leads', function (Blueprint $table) {
            $table->renameColumn('name', 'last_name');
            $table->renameColumn('company', 'workplace');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('last_name');
            $table->string('middle_name')->nullable()->after('first_name');
            $table->date('birthday')->nullable()->after('middle_name');
            $table->string('city')->nullable()->after('birthday');
            $table->string('position')->nullable()->after('workplace');
            $table->string('email')->nullable()->after('position');
            $table->timestamp('consent_at')->nullable()->after('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'middle_name', 'birthday', 'city', 'position', 'email', 'consent_at']);
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->renameColumn('last_name', 'name');
            $table->renameColumn('workplace', 'company');
        });
    }
};
