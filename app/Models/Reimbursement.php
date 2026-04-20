<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reimbursement extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'amount',
        'type',
        'attachment_path',
        'status',
        'admin_note'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
