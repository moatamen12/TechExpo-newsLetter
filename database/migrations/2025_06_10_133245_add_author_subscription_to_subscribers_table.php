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
        Schema::table('subscribers', function (Blueprint $table) {
            $table->unsignedBigInteger('author_id')->nullable()->after('user_id');
            $table->string('subscription_type', 50)->default('general')->after('author_id');
            $table->timestamp('subscribed_at')->nullable()->after('subscription_type');
            $table->timestamp('unsubscribed_at')->nullable()->after('subscribed_at');
            $table->enum('status', ['active', 'inactive', 'unsubscribed'])->default('active')->after('unsubscribed_at');
            
            // Add foreign key constraint
            $table->foreign('author_id')->references('profile_id')->on('user_profiles')->onDelete('cascade');
            
            // Add index for better performance
            $table->index(['author_id', 'status']);
            $table->index(['subscription_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropForeign(['author_id']);
            $table->dropIndex(['author_id', 'status']);
            $table->dropIndex(['subscription_type', 'status']);
            $table->dropColumn([
                'author_id',
                'subscription_type',
                'subscribed_at',
                'unsubscribed_at',
                'status'
            ]);
        });
    }
};