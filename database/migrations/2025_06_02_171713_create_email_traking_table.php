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
        Schema::create('email_tracking', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('newsletter_id');
            $table->unsignedBigInteger('subscriber_id');
            $table->boolean('opened')->default(false);
            $table->boolean('clicked')->default(false);
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            // Laravel's timestamps() method is not used here to match the SQL schema precisely.
            // If you want created_at and updated_at, add $table->timestamps();

            $table->foreign('newsletter_id')->references('id')->on('newsletter')->onDelete('cascade');
            $table->foreign('subscriber_id')->references('id')->on('subscribers')->onDelete('cascade');

            $table->index('newsletter_id');
            $table->index('subscriber_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_tracking');
    }
};