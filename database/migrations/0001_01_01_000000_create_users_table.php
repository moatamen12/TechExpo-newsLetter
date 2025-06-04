<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('user_id')->unsigned();
            $table->string('name', 100);
            $table->string('email', 100);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255);
            $table->enum('role', ['reader', 'author', 'admin'])->default('reader');
            $table->string('remember_token', 100)->nullable();
            $table->timestamps(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}