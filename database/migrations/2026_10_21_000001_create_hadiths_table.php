<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hadiths', function (Blueprint $table) {
            $table->id();
            $table->text('arabic_text');
            $table->text('english_translation');
            $table->text('urdu_translation')->nullable();
            $table->string('narrator')->nullable();
            $table->string('source_book'); // e.g. Sahih al-Bukhari 5027
            $table->string('chapter')->nullable(); // e.g. Virtues of the Quran
            $table->string('category')->default('general'); // quran, knowledge, character, prayer, patience, repentance
            $table->string('grade')->default('Sahih'); // Sahih, Hasan
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hadiths');
    }
};
