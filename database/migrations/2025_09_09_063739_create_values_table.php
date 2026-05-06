<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('values', function (Blueprint $table) {
            $table->id();
            $table->string('coin_name');      // e.g., BTC, ETH
            $table->decimal('h_value', 15, 2); // high value (big numbers + 2 decimals)
            $table->decimal('l_value', 15, 2); // low value
            $table->boolean('status')->default(1); // 1 = active, 0 = inactive
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('values');
    }
};
