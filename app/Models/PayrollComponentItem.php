<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollComponentItem extends Model
{
    protected $fillable = [
        'payroll_id',
        'payroll_component_id',
        'amount',
        'notes',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function payrollComponent()
    {
        return $this->belongsTo(PayrollComponent::class);
    }
}
