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
        Schema::create('t_invitations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('theme_id');
            $table->string('domain', 100);
            $table->binary('cover')->comment('for picture in SEO')->nullable();
            $table->string('heading', 100)->comment('for title in SEO')->nullable();
            $table->string('introduction', 100)->comment('for description in SEO')->nullable();
            
            // event
            $table->date('first_event_date')->nullable();
            $table->time('first_event_time')->nullable();
            $table->string('first_event_gmt', 5)->nullable();
            $table->text('first_event_address')->nullable();

            // music
            $table->string('music')->nullable();
            $table->longText('music_embed')->nullable();
            $table->tinyInteger('is_music_status')->default(1);
            $table->tinyInteger('is_music_type')->default(0)->comment('if music custom = 1');

            $table->tinyInteger('is_active')->default(1);
            $table->tinyInteger('is_favorite')->default(0);
            $table->tinyInteger('is_portofolio')->default(1);
            $table->tinyInteger('is_watermark')->default(1)->comment('for show/hide footer watermark');
            $table->tinyInteger('is_comment_form')->default(1)->comment('for show/hide comment form');;
            $table->binary('photo')->nullable();
            $table->binary('partner_photo')->comment('if reseller')->nullable();
            $table->tinyInteger('version')->default(1);
            $table->timestamps();

            // indexes
            $table->index('customer_id');
            $table->index('category_id');
            $table->index('theme_id');
            $table->index('domain');

            // foreign key
            $table->foreign('customer_id')->references('id')->on('m_customers')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('category_id')->references('id')->on('m_categories')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('theme_id')->references('id')->on('m_themes')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_invitations');
    }
};
