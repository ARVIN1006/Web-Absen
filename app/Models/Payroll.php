<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'user_id',
        'payroll_number',
        'month',
        'year',
        'attendance_days',
        'basic_salary',
        'overtime_pay',
        'bonus',
        'deductions',
        'total_earnings',
        'total_deductions',
        'net_salary',
        'status',
        'paid_at',
        'period_start',
        'period_end',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'date',
            'period_start' => 'date',
            'period_end' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function componentItems()
    {
        return $this->hasMany(PayrollComponentItem::class);
    }
}
