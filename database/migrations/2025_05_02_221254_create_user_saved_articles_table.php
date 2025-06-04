<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_saved_articles', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('article_id');
            $table->timestamp('saved_at')->useCurrent();
            
            $table->primary(['user_id', 'article_id']);
            
            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('article_id')
                  ->references('article_id')
                  ->on('articles')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_saved_articles');
    }
};