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
        Schema::create('t_blogs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            $table->id();
            $table->string('name', 200);
            $table->string('slug');
            $table->string('topic', 200)->nullable();
            $table->string('intro')->nullable();
            $table->longText('content');
            $table->text('source')->nullable();
            $table->string('written_by');
            $table->binary('featured_image')->nullable();
            $table->integer('hit')->default(0);
            $table->text('tags')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->tinyInteger('is_publish')->default(0);
            $table->dateTime('published_date')->nullable();
            $table->tinyInteger('is_delete')->default(0);
            $table->dateTime('deleted_date')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();

            // indexes
            $table->index('slug');
            $table->index('deleted_by');
            $table->index('created_by');
            $table->index('modified_by');

            // foreign key
            $table->foreign('deleted_by')->references('id')->on('c_users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('c_users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('modified_by')->references('id')->on('c_users')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_blogs');
    }
};
