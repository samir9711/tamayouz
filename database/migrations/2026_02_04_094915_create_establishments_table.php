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
        Schema::create('establishments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['restaurant', 'hotel', 'shop', 'medical', 'cafe', 'education', 'other'])->default('other');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('location')->nullable(); // human-readable location/address
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lon', 10, 7)->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('main_image')->nullable();
            $table->json('images')->nullable(); // additional images array
            $table->json('conditions')->nullable(); // شروط المنشأة مصفوفة/JSON
            $table->string('website')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('instagram')->nullable();
            $table->string('youtube')->nullable();
            $table->string('linkedin')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['city']);
            $table->index(['lat', 'lon']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('establishments');
    }
};
