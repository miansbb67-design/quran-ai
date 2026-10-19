<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingProgress extends Model
{
    protected $table = 'reading_progress';

    protected $fillable = [
        'user_id',
        'last_surah_number',
        'last_ayah_number',
        'total_ayahs_read',
        'total_reading_time_seconds',
        'current_streak_days',
        'last_read_date',
        'longest_streak_days',
    ];

    protected function casts(): array
    {
        return [
            'last_read_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Update the reading streak based on last read date.
     */
    public function updateStreak(): void
    {
        $today = now()->toDateString();

        if ($this->last_read_date === null) {
            $this->current_streak_days = 1;
        } elseif ($this->last_read_date->toDateString() === $today) {
            // Already read today, no change
            return;
        } elseif ($this->last_read_date->toDateString() === now()->subDay()->toDateString()) {
            $this->current_streak_days++;
        } else {
            $this->current_streak_days = 1;
        }

        if ($this->current_streak_days > $this->longest_streak_days) {
            $this->longest_streak_days = $this->current_streak_days;
        }

        $this->last_read_date = $today;
        $this->save();
    }
}
