<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiUsageLog extends Model
{
    protected $fillable = [
        'user_id',
        'service',
        'endpoint',
        'response_code',
        'response_time_ms',
        'error_message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
