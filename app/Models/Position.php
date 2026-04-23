<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = ['name', 'code', 'grade', 'salary', 'allowance', 'overtime_rate', 'is_active'];

    protected function casts(): array
    {
        return [
            'salary' => 'decimal:2',
            'allowance' => 'decimal:2',
            'overtime_rate' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
