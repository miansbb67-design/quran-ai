<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'preferred_reciter',
        'preferred_translation',
        'show_transliteration',
        'show_translation',
        'daily_goal_minutes',
        'daily_ayah_goal',
        'playback_speed',
        'auto_play_next',
        'theme',
    ];

    protected function casts(): array
    {
        return [
            'show_transliteration' => 'boolean',
            'show_translation' => 'boolean',
            'auto_play_next' => 'boolean',
            'playback_speed' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
