<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeProfile extends Model
{
    protected $fillable = [
        'user_id',
        'employee_code',
        'identity_number',
        'tax_number',
        'passport_number',
        'place_of_birth',
        'birth_date',
        'gender',
        'marital_status',
        'religion',
        'nationality',
        'current_address',
        'domicile_address',
        'phone_number',
        'alternate_phone_number',
        'personal_email',
        'avatar_path',
        'face_reference_path',
        'face_descriptor',
        'joined_at',
        'probation_end_at',
        'contract_start_at',
        'contract_end_at',
        'resigned_at',
        'employment_status',
        'blood_type',
        'shirt_size',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'joined_at' => 'date',
            'probation_end_at' => 'date',
            'contract_start_at' => 'date',
            'contract_end_at' => 'date',
            'resigned_at' => 'date',
            'face_descriptor' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
