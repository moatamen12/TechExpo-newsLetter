<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_followers', function (Blueprint $table) {
            $table->unsignedBigInteger('follower_id');
            $table->unsignedBigInteger('followed_id');
            $table->timestamp('created_at')->useCurrent();
            
            $table->primary(['follower_id', 'followed_id']);
            
            $table->foreign('follower_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('followed_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_followers');
    }
};