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
        Schema::create('notification_targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notification_id');
            $table->enum('target_type', ['user','directorate','ministry','all']);
            $table->unsignedBigInteger('target_id')->nullable(); // nullable عندما target_type = 'all'
            $table->timestamps();

            $table->foreign('notification_id')->references('id')->on('notifications')->onDelete('cascade');
            $table->index(['target_type','target_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_targets');
    }
};
