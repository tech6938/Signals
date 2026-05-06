<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSignalsTable extends Migration
{
    public function up()
    {
        Schema::create('signals', function (Blueprint $table) {
            $table->id();
            $table->string('coin_name');
            $table->decimal('b_price', 15, 8);
            $table->decimal('tp1', 15, 8)->nullable();
            $table->decimal('tp2', 15, 8)->nullable();
            $table->decimal('tp3', 15, 8)->nullable();
            $table->decimal('tp4', 15, 8)->nullable();
            $table->string('icon1')->nullable();
            $table->string('icon2')->nullable();
            $table->decimal('last_price', 15, 8)->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('signals');
    }
}
