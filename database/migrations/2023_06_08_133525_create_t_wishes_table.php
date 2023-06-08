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
        Schema::create('t_wishes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('invitation_id');
            $table->string('name');
            $table->longText('message');
            $table->timestamps();

            // foreign key
            $table->foreign('customer_id')->references('id')->on('m_customers')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('invitation_id')->references('id')->on('t_invitations')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_wishes');
    }
};
