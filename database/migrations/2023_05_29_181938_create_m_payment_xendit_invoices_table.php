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
        Schema::create('m_payment_xendit_invoices', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            $table->id();
            $table->string('invoice_id')->unique('invoice_id');
            $table->string('external_id')->unique('external_id');
            $table->string('user_id');
            $table->boolean('is_high')->default(false);
            $table->string('status');
            $table->string('merchant_name');
            $table->bigInteger('amount');
            $table->string('payer_email')->nullable();
            $table->string('description')->nullable();
            $table->bigInteger('paid_amount')->nullable();
            $table->string('updated');
            $table->string('created');
            $table->string('currency');
            $table->string('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_channel')->nullable();
            $table->string('payment_destination')->nullable();
            $table->json('payment_details')->nullable();
            $table->string('payment_id')->unique('payment_id');
            $table->string('success_redirect_url');
            $table->string('failure_redirect_url');
            $table->string('credit_card_charge_id')->nullable();
            $table->json('items')->nullable();
            $table->json('fees')->nullable();
            $table->boolean('should_authenticate_credit_card')->nullable()->default(false);
            $table->string('bank_code')->nullable();
            $table->string('ewallet_type')->nullable();
            $table->string('on_demand_link')->nullable();
            $table->string('recurring_payment_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_payment_xendit_invoices');
    }
};
