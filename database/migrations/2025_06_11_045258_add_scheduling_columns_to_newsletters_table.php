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
        Schema::table('newsletters', function (Blueprint $table) {
            if (!Schema::hasColumn('newsletters', 'scheduled_at')) {
                $table->timestamp('scheduled_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('newsletters', 'total_sent')) {
                $table->integer('total_sent')->default(0)->after('scheduled_at');
            }
            if (!Schema::hasColumn('newsletters', 'total_failed')) {
                $table->integer('total_failed')->default(0)->after('total_sent');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newsletters', function (Blueprint $table) {
            $table->dropColumn(['scheduled_at', 'total_sent', 'total_failed']);
        });
    }
};
