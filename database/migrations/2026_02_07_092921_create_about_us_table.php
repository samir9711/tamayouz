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
        Schema::create('about_us', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();


            // المحتوى الرئيسي (HTML أو نص طويل)
            $table->longText('content')->nullable();

            // حقول اختيارية لعرض مهمة/رؤية/قِيَم قصيرة
            $table->text('mission')->nullable();
            $table->text('vision')->nullable();
            $table->json('values')->nullable(); // قائمة القيم  (مثلاً ["ابتكار","التفاني"])


            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('address')->nullable();
            $table->string('location')->nullable(); // نص قابل للعرض

            // إحداثيات إن أردت خرائط
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lon', 10, 7)->nullable();

            // صور
            $table->string('main_image')->nullable();
            $table->json('images')->nullable(); // مصفوفة روابط/مسارات صور


            $table->string('website')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('instagram')->nullable();
            $table->string('youtube')->nullable();
            $table->string('linkedin')->nullable();

            $table->timestamps();
            $table->softDeletes();



        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_us');
    }
};
