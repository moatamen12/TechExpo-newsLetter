<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->bigIncrements('profile_id');
            $table->unsignedBigInteger('user_id');
            $table->string('profile_photo')->nullable();
            $table->text('bio')->nullable();
            $table->string('work', 100)->nullable();
            $table->string('website')->nullable();
            $table->enum('social_links', ['facebook', 'twitter', 'instagram', 'linkedin'])->nullable();
            $table->integer('followers_count')->default(0);
            $table->integer('num_articles')->default(0);
            $table->integer('reactions_count')->default(0);
            $table->timestamps();
            
            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_profiles');
    }
};