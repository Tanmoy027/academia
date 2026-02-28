<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('live_session_link')->nullable()->after('online_schedule');
            $table->text('live_session_message')->nullable()->after('live_session_link');
            $table->timestamp('live_session_expires_at')->nullable()->after('live_session_message');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['live_session_link', 'live_session_message', 'live_session_expires_at']);
        });
    }
};
