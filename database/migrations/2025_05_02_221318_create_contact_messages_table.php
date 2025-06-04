<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('username');
            $table->string('email');
            $table->string('subject')->nullable();
            $table->enum('message_category', ['general', 'complaint', 'Suggestion', 'Technical Support'])->default('general');
            $table->enum('message_statue', ['pending', 'dealt_with'])->default('pendeing');
            $table->text('message');
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_messages');
    }
};