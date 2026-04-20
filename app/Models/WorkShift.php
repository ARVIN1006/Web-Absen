<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkShift extends Model
{
    protected $fillable = [
        'name',
        'clock_in_time',
        'clock_out_time',
        'late_tolerance_minutes',
        'is_default',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
