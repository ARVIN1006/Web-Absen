<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmploymentType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'category',
        'description',
        'is_active',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
