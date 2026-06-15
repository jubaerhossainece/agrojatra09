<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class MonthlyPayment extends Model
{
    protected $fillable = [
        'member_id', 'payment_year', 'payment_month',
        'expected_amount', 'due_date', 'is_late',
    ];

    protected function casts(): array
    {
        return [
            'due_date'    => 'date',
            'is_late'     => 'boolean',
            'expected_amount' => 'decimal:2',
        ];
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function allocations()
    {
        return $this->hasMany(DepositAllocation::class);
    }

    protected function totalAllocated(): Attribute
    {
        return Attribute::get(fn () => $this->allocations->sum('allocated_amount'));
    }

    protected function balance(): Attribute
    {
        return Attribute::get(fn () => max(0, $this->expected_amount - $this->total_allocated));
    }

    protected function isPaid(): Attribute
    {
        return Attribute::get(fn () => $this->total_allocated >= $this->expected_amount);
    }

    protected function monthLabel(): Attribute
    {
        return Attribute::get(fn () => \Carbon\Carbon::create($this->payment_year, $this->payment_month, 1)->format('M Y'));
    }
}
