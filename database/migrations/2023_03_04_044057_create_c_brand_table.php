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
        Schema::create('c_brand', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('email', 100)->unique('email')->nullable();
            $table->text('address')->nullable();
            $table->string('phone_code', 5)->nullable();
            $table->string('phone_dial_code', 10)->nullable();
            $table->string('phone', 15)->nullable();
            $table->binary('logo')->nullable();
            $table->string('logo_ext', 5)->nullable();
            $table->binary('logo_light')->nullable();
            $table->string('logo_light_ext', 5)->nullable();
            $table->binary('favicon')->nullable();
            $table->string('favicon_ext', 5)->nullable();
            $table->string('facebook')->nullable();
            $table->string('youtube')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('twitter')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('c_brand');
    }
};
