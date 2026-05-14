<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Share extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'number_of_shares',
        'total_amount',
        'status',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function getTotalDepositedAttribute()
    {
        return $this->deposits()->sum('amount');
    }

    public function getBalanceDueAttribute()
    {
        return $this->total_amount - $this->total_deposited;
    }
}
