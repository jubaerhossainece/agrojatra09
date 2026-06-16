<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShareChangeRequest extends Model
{
    protected $fillable = [
        'member_id',
        'requested_by',
        'old_shares',
        'new_shares',
        'status',
        'admin_note',
        'member_note',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
