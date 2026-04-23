<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    protected $fillable = [
        'user_id',
        'document_type',
        'document_number',
        'file_path',
        'issued_at',
        'expired_at',
        'notes',
        'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'expired_at' => 'date',
            'is_verified' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
