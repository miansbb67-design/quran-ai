<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemorizationProgress extends Model
{
    protected $table = 'memorization_progress';

    protected $fillable = [
        'user_id',
        'surah_number',
        'ayah_number',
        'status',
        'total_practice_count',
        'consecutive_correct',
        'confidence_level',
        'last_practiced_at',
        'next_review_at',
        'review_interval_days',
    ];

    protected function casts(): array
    {
        return [
            'last_practiced_at' => 'datetime',
            'next_review_at' => 'datetime',
            'confidence_level' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Update memorization using spaced repetition logic.
     */
    public function recordPractice(string $result): void
    {
        $this->total_practice_count++;
        $this->last_practiced_at = now();

        if ($result === 'correct') {
            $this->consecutive_correct++;
            $this->confidence_level = min(100, $this->confidence_level + 5);

            // Increase review interval (spaced repetition)
            $this->review_interval_days = min(30, $this->review_interval_days * 2);

            // Update status based on consecutive correct attempts
            if ($this->consecutive_correct >= 5 && $this->status !== 'memorized') {
                $this->status = 'memorized';
            } elseif ($this->consecutive_correct >= 2 && $this->status === 'learning') {
                $this->status = 'practicing';
            }
        } elseif ($result === 'incorrect') {
            $this->consecutive_correct = 0;
            $this->confidence_level = max(0, $this->confidence_level - 10);
            $this->review_interval_days = 1;

            if ($this->status === 'memorized') {
                $this->status = 'practicing';
            }
        }
        // 'uncertain' — slight penalty
        else {
            $this->confidence_level = max(0, $this->confidence_level - 3);
            $this->review_interval_days = max(1, intval($this->review_interval_days / 2));
        }

        $this->next_review_at = now()->addDays($this->review_interval_days);
        $this->save();
    }
}
