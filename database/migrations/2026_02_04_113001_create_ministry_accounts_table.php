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
        Schema::create('ministry_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ministry_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['min_admin', 'min_subadmin'])->default('min_subadmin');
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('ministry_id')->references('id')->on('ministries')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ministry_accounts');
    }
};
