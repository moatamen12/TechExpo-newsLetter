<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('article_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('article_id')->constrained('articles', 'article_id')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'article_id']); // Ensure a user can only like an article once
        });
    }

    public function down()
    {
        Schema::dropIfExists('article_likes');
    }
};