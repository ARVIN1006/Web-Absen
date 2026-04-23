<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reimbursement extends Model
{
    protected $fillable = [
        'user_id',
        'request_number',
        'title',
        'description',
        'amount',
        'type',
        'attachment_path',
        'status',
        'admin_note',
        'approved_by',
        'approved_at',
        'reimbursed_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'reimbursed_at' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
