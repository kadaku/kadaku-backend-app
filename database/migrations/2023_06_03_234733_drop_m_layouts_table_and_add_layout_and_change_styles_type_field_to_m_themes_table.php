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
        Schema::dropIfExists('m_layouts');

        Schema::table('m_themes', function (Blueprint $table) {
            $table->string('layout', 100)->nullable();
            $table->json('styles')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_themes', function (Blueprint $table) {
            //
        });
    }
};
