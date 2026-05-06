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
        Schema::create('news', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('title'); // News title
            $table->text('description'); // News description
            $table->string('image')->nullable(); // Image URL or path
            $table->string('url')->nullable(); // Optional external URL
            $table->boolean('status')->default(1); // Status: 1=active, 0=inactive
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
