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
            $table->unsignedBigInteger('author_id')->nullable()->after('id');
            $table->enum('recipient_type', ['all', 'selected', 'subscribers_only'])
                  ->default('all')->after('status');
            $table->json('selected_subscribers')->nullable()->after('recipient_type');
            $table->integer('total_sent')->default(0)->after('sent_at');
            $table->integer('total_failed')->default(0)->after('total_sent');
            
            $table->foreign('author_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newsletter', function (Blueprint $table) {
            $table->dropForeign(['author_id']);
            $table->dropColumn([
                'author_id',
                'recipient_type', 
                'selected_subscribers',
                'total_sent',
                'total_failed'
            ]);
        });
    }
};
