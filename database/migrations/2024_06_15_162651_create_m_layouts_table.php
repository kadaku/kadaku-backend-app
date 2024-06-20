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
        Schema::create('m_layouts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            
            $table->id();
            $table->unsignedBigInteger('category_layout_id');
            $table->string('title');
            $table->longText('icon')->nullable();
            $table->binary('image')->nullable();
            $table->longText('body')->nullable();
            $table->tinyInteger('is_premium')->default(0);
            $table->tinyInteger('order')->default(0);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();

            // indexes
            $table->index('category_layout_id');

            // foreign key
            $table->foreign('category_layout_id')->references('id')->on('m_categories_layouts')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_layouts');
    }
};
