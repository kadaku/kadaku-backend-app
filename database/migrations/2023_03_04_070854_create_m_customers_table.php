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
            $table->string('phone_code', 5)->nullable();;
            $table->string('phone_dial_code', 10)->nullable();;
            $table->string('phone_domestic', 60)->nullable();
            $table->string('phone_iso2', 5)->nullable();;
            $table->string('phone', 20)->nullable();;
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

            // update field
            $table->bigInteger('referral_id')->nullable()->comment('id customer recommendation');
            $table->tinyInteger('is_trial')->default(1);
            $table->tinyInteger('is_premium')->default(0);
            $table->tinyInteger('is_reseller')->default(0);
            $table->string('reseller_name', 100)->nullable();
            $table->text('reseller_bio')->nullable();
            $table->binary('reseller_logo')->nullable();
            $table->double('saldo')->default(0);
            $table->double('total_withdrawal')->default(0);
            $table->tinyInteger('is_subscription')->default(1);

            $table->dateTime('start_at');
            $table->dateTime('expired_at');


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
