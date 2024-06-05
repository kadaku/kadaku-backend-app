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
        Schema::create('m_musics', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            $table->id();
            $table->unsignedBigInteger('category_music_id')->nullable();
            $table->string('name');
            $table->binary('file')->nullable();
            $table->longText('categories')->nullable();
            $table->longText('file_url')->nullable();
            $table->unsignedBigInteger('temp_id')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();

            // indexes
            $table->index('category_music_id');
            $table->index('created_by');
            $table->index('modified_by');

            // foreign key
            $table->foreign('category_music_id')->references('id')->on('m_categories_musics')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('c_users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('modified_by')->references('id')->on('c_users')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_musics');
    }
};
