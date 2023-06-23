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
        Schema::create('m_theme_components', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            $table->id();
            $table->unsignedBigInteger('theme_id')->default(0);
            $table->unsignedBigInteger('invitation_id')->default(0);
            $table->unsignedBigInteger('customer_id')->default(0);
            $table->string('name', 100)->nullable();
            $table->enum('type', ['section', 'utility'])->default('section');
            $table->string('ref', 100)->nullable();
            $table->tinyInteger('order')->default(0);
            $table->json('props')->nullable();
            $table->longText('icon')->nullable();
            $table->tinyInteger('is_icon')->default(1)->comment('for show hide icon');
            $table->binary('thumbnail')->nullable();
            $table->tinyInteger('is_premium')->default(0);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_theme_components');
    }
};
