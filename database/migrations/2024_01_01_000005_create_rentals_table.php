<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('ride_id')->constrained('rides')->restrictOnDelete();
            $table->smallInteger('status')->default(0); // 0=active,1=completed,2=cancelled
            $table->dateTime('start_at');
            $table->dateTime('end_at')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->unsignedInteger('life_jacket_quantity')->default(0);
            $table->string('note')->nullable();
            
            // Snapshot fields for historical data
            $table->string('user_name_at_time')->nullable();
            $table->string('ride_identifier_at_time')->nullable();
            $table->string('classification_name_at_time')->nullable();
            $table->decimal('price_per_hour_at_time', 10, 2);
            $table->decimal('computed_total', 10, 2);
            
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('ride_id');
            $table->index('status');
            $table->index('start_at');
            $table->index('end_at');
            $table->index(['ride_id', 'status']);
            $table->index(['start_at', 'end_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
