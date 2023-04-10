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
        Schema::create('c_users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            $table->id();
            $table->unsignedBigInteger('user_group_id');
            $table->string('name');
            $table->string('email')->unique('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone_code', 5)->nullable();
            $table->string('phone_dial_code', 10)->nullable();
            $table->string('phone', 20)->nullable();
            $table->binary('photo')->nullable();
            $table->string('photo_ext', 5)->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();

            // indexes
            $table->index('user_group_id');

            // foreign key
            $table->foreign('user_group_id')->references('id')->on('c_user_groups')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('c_users');
    }
};
