<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id', 255)->primary();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address', 45);
            $table->text('user_agent');
            $table->longText('payload');
            $table->unsignedInteger('last_activity');

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
