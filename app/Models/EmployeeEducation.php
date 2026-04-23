<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeEducation extends Model
{
    protected $fillable = [
        'user_id',
        'education_level',
        'institution_name',
        'major',
        'start_year',
        'end_year',
        'gpa',
        'is_latest',
    ];

    protected function casts(): array
    {
        return [
            'is_latest' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
