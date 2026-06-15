<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'share_id',
        'amount',
        'deposit_date',
        'bank_name',
        'bank_reference',
        'receipt_number',
        'note',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'deposit_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function share()
    {
        return $this->belongsTo(Share::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function allocations()
    {
        return $this->hasMany(DepositAllocation::class);
    }
}
