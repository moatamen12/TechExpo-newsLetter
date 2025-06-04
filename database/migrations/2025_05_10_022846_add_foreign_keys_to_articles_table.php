<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->foreign('author_id')
                  ->references('profile_id')
                  ->on('user_profiles')
                  ->onDelete('cascade');
                  
            $table->foreign('category_id')
                  ->references('category_id')
                  ->on('categories');
        });
    }

    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['author_id']);
            $table->dropForeign(['category_id']);
        });
    }
};