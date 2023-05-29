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
        Schema::create('m_customers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            $table->id();
            $table->string('name');
            $table->string('email')->unique('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone_code', 5);
            $table->string('phone_dial_code', 10);
            $table->string('phone_domestic', 60);
            $table->string('phone_iso2', 5);
            $table->string('phone', 20);
            $table->text('address')->nullable();
            $table->integer('province_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('district_id')->nullable();
            $table->integer('subdistrict_id')->nullable();
            $table->integer('post_code', 6)->nullable();
            $table->binary('photo')->nullable();
            $table->string('photo_ext', 4)->nullable();
            $table->binary('avatar')->nullable();
            $table->tinyInteger('is_active')->default(0);
            $table->string('known_source', 50)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_customers');
    }
};
