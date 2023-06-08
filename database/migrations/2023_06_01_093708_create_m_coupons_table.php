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
        Schema::create('m_coupons', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('code');
            $table->dateTime('periode_start')->nullable();
            $table->dateTime('periode_end')->nullable();
            $table->double('amount')->comment('number of coupons');
            $table->double('minimum_amount')->default(0)->comment('for rules minimum transaction');
            $table->tinyInteger('is_private')->default(0)->comment('for flag not showing in public');
            $table->tinyInteger('is_showing')->default(1)->comment('for flag showing in public');
            $table->tinyInteger('is_active')->default(1);
            $table->binary('thumbnail')->nullable();
            $table->bigInteger('user_id')->nullable()->comment('user create this');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_coupons');
    }
};
