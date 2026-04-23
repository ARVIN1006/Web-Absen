<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    protected $fillable = [
        'user_id',
        'leave_type_id',
        'year',
        'allocated_days',
        'used_days',
        'reserved_days',
        'remaining_days',
    ];

    protected function casts(): array
    {
        return [
            'allocated_days' => 'decimal:2',
            'used_days' => 'decimal:2',
            'reserved_days' => 'decimal:2',
            'remaining_days' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
}
