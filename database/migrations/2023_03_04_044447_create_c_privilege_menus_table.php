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
        Schema::create('c_privilege_menus', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            
            $table->id();
            $table->unsignedBigInteger('user_group_id');
            $table->unsignedBigInteger('menu_id');
            $table->timestamps();

            // indexes
            $table->index('user_group_id');
            $table->index('menu_id');

            // foreign key
            $table->foreign('user_group_id')->references('id')->on('c_user_groups')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('menu_id')->references('id')->on('c_menus')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('c_privilege_menus');
    }
};
