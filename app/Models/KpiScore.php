<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiScore extends Model
{
    protected $fillable = [
        'user_id',
        'period',
        'attendance_score',
        'performance_score',
        'attitude_score',
        'feedback'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
