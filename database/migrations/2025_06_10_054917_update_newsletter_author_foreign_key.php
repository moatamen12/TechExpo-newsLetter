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
        Schema::table('newsletter', function (Blueprint $table) {
            // Drop the existing foreign key if it exists
            $table->dropForeign(['author_id']);
            
            // Add the new foreign key that references user_profiles
            $table->foreign('author_id')
                  ->references('profile_id')
                  ->on('user_profiles')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newsletter', function (Blueprint $table) {
            // Drop the current foreign key
            $table->dropForeign(['author_id']);
            
            // Restore the original foreign key (if needed)
            $table->foreign('author_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
};
