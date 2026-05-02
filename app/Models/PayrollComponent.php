<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollComponent extends Model
{
    protected $fillable = [
        'name',
        'code',
        'type',
        'calculation_method',
        'default_amount',
        'is_taxable',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_taxable' => 'boolean',
            'is_active' => 'boolean',
            'default_amount' => 'decimal:2',
        ];
    }

    public function items()
    {
        return $this->hasMany(PayrollComponentItem::class);
    }
}
