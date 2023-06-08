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
        Schema::rename('m_payment_xendit_invoices', 't_payment_xendit_invoices');

        Schema::table('t_payment_xendit_invoices', function (Blueprint $table) {
            $table->string('invoice_url')->nullable();
            $table->integer('reminder_date')->nullable();
            $table->string('expiry_date', 100)->nullable();
            $table->dropUnique('payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_payment_xendit_invoices', function (Blueprint $table) {
            //
        });
    }
};
