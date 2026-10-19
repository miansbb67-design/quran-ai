<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticeAttempt extends Model
{
    protected $fillable = [
        'practice_session_id',
        'user_id',
        'surah_number',
        'ayah_number',
        'attempt_number',
        'recognized_text',
        'confidence_score',
        'result',
        'word_analysis',
        'feedback_message',
    ];

    protected function casts(): array
    {
        return [
            'word_analysis' => 'array',
            'confidence_score' => 'decimal:2',
        ];
    }

    public function session()
    {
        return $this->belongsTo(PracticeSession::class, 'practice_session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
