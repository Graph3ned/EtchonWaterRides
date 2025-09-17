<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            if (!Schema::hasColumn('rentals', 'ride_type_name_at_time')) {
                $table->string('ride_type_name_at_time')->nullable()->after('classification_name_at_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            if (Schema::hasColumn('rentals', 'ride_type_name_at_time')) {
                $table->dropColumn('ride_type_name_at_time');
            }
        });
    }
};


