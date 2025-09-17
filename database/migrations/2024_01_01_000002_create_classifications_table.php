<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ride_type_id')->constrained('ride_types')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price_per_hour', 10, 2);
            $table->timestamps();

            $table->unique(['ride_type_id', 'name']);
            $table->index('ride_type_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classifications');
    }
};
