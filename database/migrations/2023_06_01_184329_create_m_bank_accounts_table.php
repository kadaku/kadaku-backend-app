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
        Schema::create('m_bank_accounts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            $table->id();
            $table->string('name');
            $table->string('code');
            $table->string('method');
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->binary('logo')->nullable();
            $table->tinyInteger('is_invoice')->default(0)->comment('for invoice');
            $table->tinyInteger('is_digital_envelope')->default(0)->comment('for digital envelope');
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_bank_accounts');
    }
};
