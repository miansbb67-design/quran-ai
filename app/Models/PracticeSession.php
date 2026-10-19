<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticeSession extends Model
{
    protected $fillable = [
        'user_id',
        'session_type',
        'surah_number',
        'start_ayah',
        'end_ayah',
        'duration_seconds',
        'overall_score',
        'session_data',
    ];

    protected function casts(): array
    {
        return [
            'session_data' => 'array',
            'overall_score' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attempts()
    {
        return $this->hasMany(PracticeAttempt::class);
    }
}
