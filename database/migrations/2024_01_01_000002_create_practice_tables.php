<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practice_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('session_type', ['reading', 'recitation', 'memorization']);
            $table->integer('surah_number');
            $table->integer('start_ayah');
            $table->integer('end_ayah');
            $table->integer('duration_seconds')->default(0);
            $table->decimal('overall_score', 5, 2)->nullable();
            $table->json('session_data')->nullable();
            $table->timestamps();
        });

        Schema::create('practice_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('surah_number');
            $table->integer('ayah_number');
            $table->integer('attempt_number')->default(1);
            $table->text('recognized_text')->nullable();
            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->enum('result', ['correct', 'uncertain', 'incorrect'])->nullable();
            $table->json('word_analysis')->nullable();
            $table->text('feedback_message')->nullable();
            $table->timestamps();
        });

        Schema::create('memorization_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('surah_number');
            $table->integer('ayah_number');
            $table->enum('status', ['not_started', 'learning', 'practicing', 'memorized'])->default('not_started');
            $table->integer('total_practice_count')->default(0);
            $table->integer('consecutive_correct')->default(0);
            $table->decimal('confidence_level', 5, 2)->default(0);
            $table->timestamp('last_practiced_at')->nullable();
            $table->timestamp('next_review_at')->nullable();
            $table->integer('review_interval_days')->default(1);
            $table->timestamps();

            $table->unique(['user_id', 'surah_number', 'ayah_number']);
        });

        Schema::create('revision_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('surah_number');
            $table->integer('start_ayah');
            $table->integer('end_ayah');
            $table->date('scheduled_date');
            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'scheduled_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revision_schedules');
        Schema::dropIfExists('memorization_progress');
        Schema::dropIfExists('practice_attempts');
        Schema::dropIfExists('practice_sessions');
    }
};
