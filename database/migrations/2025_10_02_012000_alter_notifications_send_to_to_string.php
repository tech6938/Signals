<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Change enum to string to support new types like value_subscribers
            $table->string('send_to', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Revert back to enum definition if needed (may fail on some platforms)
            // $table->enum('send_to', ['all', 'subscribers', 'non_subscribers', 'staff', 'individual'])->change();
        });
    }
};


