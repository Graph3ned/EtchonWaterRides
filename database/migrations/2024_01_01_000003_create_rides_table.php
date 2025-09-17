<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classification_id')->constrained('classifications')->restrictOnDelete();
            $table->string('identifier');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['classification_id', 'identifier']);
            $table->index('classification_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rides');
    }
};
