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
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->string('user');
            $table->string('rideType');
            $table->string('classification');
            $table->string('note')->nullable();
            $table->integer('duration');
            $table->integer('life_jacket_usage');
            $table->decimal('pricePerHour', 8, 2);
            $table->decimal('totalPrice', 8, 2);
            $table->integer('status');
            $table->time('timeStart');
            $table->time('timeEnd');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
