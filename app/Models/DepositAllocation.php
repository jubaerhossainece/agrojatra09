<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepositAllocation extends Model
{
    protected $fillable = ['deposit_id', 'monthly_payment_id', 'allocated_amount'];

    protected function casts(): array
    {
        return ['allocated_amount' => 'decimal:2'];
    }

    public function deposit()
    {
        return $this->belongsTo(Deposit::class);
    }

    public function monthlyPayment()
    {
        return $this->belongsTo(MonthlyPayment::class);
    }
}
