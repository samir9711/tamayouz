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
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->unsignedBigInteger('ministry_id');
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('title')->nullable();
            $table->text('description')->nullable();




            $table->timestamps();
            $table->softDeletes();


            $table->foreign('ministry_id')->references('id')->on('ministries')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');



            $table->index(['ministry_id']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
