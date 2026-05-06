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
        Schema::table('invoices', function (Blueprint $table) {
            $table->boolean('warning_notification_sent')->default(false)->after('pdf_path');
            $table->boolean('expiration_notification_sent')->default(false)->after('warning_notification_sent');
            $table->timestamp('warning_notification_sent_at')->nullable()->after('expiration_notification_sent');
            $table->timestamp('expiration_notification_sent_at')->nullable()->after('warning_notification_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'warning_notification_sent',
                'expiration_notification_sent', 
                'warning_notification_sent_at',
                'expiration_notification_sent_at'
            ]);
        });
    }
};