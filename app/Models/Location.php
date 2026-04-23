<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'branch_id',
        'name',
        'code',
        'latitude',
        'longitude',
        'radius',
        'address',
        'is_active',
        'enforce_face_verification',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'enforce_face_verification' => 'boolean',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
