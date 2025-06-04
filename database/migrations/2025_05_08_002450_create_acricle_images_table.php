<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('article_images', function (Blueprint $table) {
            $table->bigIncrements('image_id');
            $table->unsignedBigInteger('article_id');
            $table->string('filename');
            $table->string('path');
            $table->string('mime_type', 100);
            $table->unsignedInteger('size');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('alt_text')->nullable();
            $table->text('caption')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('order')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('article_id')
                  ->references('article_id')
                  ->on('articles')
                  ->onDelete('cascade');
                  
            $table->index('is_featured');
        });
    }

    public function down()
    {
        Schema::dropIfExists('article_images');
    }
};