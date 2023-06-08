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
        Schema::create('m_packages', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->binary('thumbnail')->nullable();
            $table->double('price')->default(0);
            $table->integer('discount')->default(0);
            $table->tinyInteger('is_premium')->default(0);
            $table->tinyInteger('is_reseller')->default(0);
            $table->tinyInteger('is_active')->default(1);
            $table->tinyInteger('is_recommended')->default(0);
            $table->tinyInteger('valid_days')->default(0)->comment('ex: 7');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_packages');
    }
};
