<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('value_id')->nullable()->after('send_to');
            $table->string('image_path')->nullable()->after('description');
            $table->foreign('value_id')->references('id')->on('values')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['value_id']);
            $table->dropColumn(['value_id','image_path']);
        });
    }
};


