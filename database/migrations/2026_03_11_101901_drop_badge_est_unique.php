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
        Schema::table('badge_discounts', function (Blueprint $table) {
          if (Schema::hasTable('badge_discounts')) {
            
                $table->dropUnique('badge_est_unique');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('badge_discounts', function (Blueprint $table) {
            //
        });
    }
};
