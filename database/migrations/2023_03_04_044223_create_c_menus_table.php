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
        Schema::create('c_menus', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name', 100);
            $table->string('url', 100);
            $table->string('icon', 50);
            $table->tinyInteger('sort');
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();

            // indexes
            $table->index('parent_id');
            $table->index('sort');

            // foreign key
            $table->foreign('parent_id')->references('id')->on('c_menus')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('c_menus');
    }
};
