<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeCareerHistory extends Model
{
    protected $fillable = [
        'user_id',
        'department_id',
        'position_id',
        'branch_id',
        'start_date',
        'end_date',
        'employment_status',
        'change_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
