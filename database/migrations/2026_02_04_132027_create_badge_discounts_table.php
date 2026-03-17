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
        Schema::create('badge_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('badge_id');

            $table->unsignedBigInteger('establishment_id')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('note')->nullable();
            $table->decimal('discount_percent', 5, 2)->nullable();
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_until')->nullable();
            $table->enum('status', ['active', 'revoked', 'expired'])->default('active');
            $table->json('categories')->nullable();
            $table->dateTime('scanned_at')->nullable();

            $table->timestamps();
            $table->softDeletes();



            $table->foreign('establishment_id')->references('id')->on('establishments')->onDelete('set null');
            $table->foreign('badge_id')->references('id')->on('badges')->onDelete('cascade');


            $table->index(['badge_id']);
            $table->index(['establishment_id']);
            $table->index(['status']);
            $table->unique(['badge_id','establishment_id'],'badge_est_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badge_discounts');
    }
};
