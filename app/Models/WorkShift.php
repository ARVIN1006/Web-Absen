<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkShift extends Model
{
    protected $fillable = [
        'branch_id',
        'name',
        'clock_in_time',
        'clock_out_time',
        'break_start_time',
        'break_end_time',
        'late_tolerance_minutes',
        'work_days',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'work_days' => 'array',
            'is_default' => 'boolean',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
