<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningGoal extends Model
{
    protected $fillable = [
        'user_id',
        'goal_type',
        'target_value',
        'target_unit',
        'current_value',
        'goal_date',
        'completed',
    ];

    protected function casts(): array
    {
        return [
            'goal_date' => 'date',
            'completed' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function progressPercentage(): float
    {
        if ($this->target_value <= 0) return 0;
        return min(100, round(($this->current_value / $this->target_value) * 100, 1));
    }
}
