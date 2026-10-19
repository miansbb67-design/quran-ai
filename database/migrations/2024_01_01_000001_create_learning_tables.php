<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('preferred_reciter')->default('ar.alafasy');
            $table->string('preferred_translation')->default('en.asad');
            $table->boolean('show_transliteration')->default(true);
            $table->boolean('show_translation')->default(true);
            $table->integer('daily_goal_minutes')->default(15);
            $table->integer('daily_ayah_goal')->default(5);
            $table->decimal('playback_speed', 3, 2)->default(1.00);
            $table->boolean('auto_play_next')->default(false);
            $table->string('theme')->default('light');
            $table->timestamps();
        });

        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('surah_number');
            $table->integer('ayah_number');
            $table->string('note')->nullable();
            $table->string('color')->default('green');
            $table->timestamps();

            $table->unique(['user_id', 'surah_number', 'ayah_number']);
        });

        Schema::create('reading_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('last_surah_number')->default(1);
            $table->integer('last_ayah_number')->default(1);
            $table->integer('total_ayahs_read')->default(0);
            $table->integer('total_reading_time_seconds')->default(0);
            $table->integer('current_streak_days')->default(0);
            $table->date('last_read_date')->nullable();
            $table->integer('longest_streak_days')->default(0);
            $table->timestamps();
        });

        Schema::create('learning_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('goal_type', ['daily_reading', 'memorization', 'practice']);
            $table->integer('target_value');
            $table->string('target_unit')->default('ayahs');
            $table->integer('current_value')->default(0);
            $table->date('goal_date');
            $table->boolean('completed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_goals');
        Schema::dropIfExists('reading_progress');
        Schema::dropIfExists('bookmarks');
        Schema::dropIfExists('user_preferences');
    }
};
