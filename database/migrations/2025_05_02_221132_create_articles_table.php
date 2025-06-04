<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->bigIncrements('article_id');
            $table->unsignedBigInteger('author_id');
            $table->unsignedBigInteger('category_id');
            $table->string('title');
            $table->longText('content');
            $table->text('summary')->nullable();
            $table->string('featured_image_url')->nullable();
            $table->unsignedInteger('like_count')->default(0);
            $table->unsignedInteger('comment_count')->default(0);
            $table->unsignedInteger('view_count')->default(0);
            $table->enum('status', ['draft', 'scheduled', 'published'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            

            $table->index('status');
            $table->index('scheduled_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('articles');
    }
};