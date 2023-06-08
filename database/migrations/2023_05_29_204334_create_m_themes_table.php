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
        Schema::create('m_themes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('type_id');
            $table->string('name', 100);
            $table->string('slug', 100);
            $table->text('description')->nullable();
            $table->longText('styles')->nullable()->comment('for style css');
            $table->binary('background')->nullable();
            $table->binary('thumbnail')->nullable();
            $table->binary('thumbnail_xs')->nullable();
            $table->double('price')->default(0);
            $table->integer('discount')->default(0);
            $table->tinyInteger('is_premium')->default(1);
            $table->tinyInteger('version')->default(1);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();

            // indexes
            $table->index('category_id');
            $table->index('type_id');

            // foreign key
            $table->foreign('category_id')->references('id')->on('m_categories')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('type_id')->references('id')->on('m_themes_type')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_themes');
    }
};