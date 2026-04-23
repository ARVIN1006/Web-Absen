<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'code',
        'phone_number',
        'email',
        'address',
        'city',
        'province',
        'postal_code',
        'is_head_office',
        'is_active',
    ];

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function workShifts()
    {
        return $this->hasMany(WorkShift::class);
    }
}
