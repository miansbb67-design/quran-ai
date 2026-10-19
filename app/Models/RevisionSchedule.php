<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RevisionSchedule extends Model
{
    protected $fillable = [
        'user_id',
        'surah_number',
        'start_ayah',
        'end_ayah',
        'scheduled_date',
        'completed',
        'completed_at',
        'score',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'completed' => 'boolean',
            'completed_at' => 'datetime',
            'score' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
