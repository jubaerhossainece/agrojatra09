<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'permanent_address',
        'present_address',
        'profession',
        'company_name',
        'company_address',
        'blood_group',
        'photo',
        'status',
        'admin_deposit_permission',
    ];

    protected function casts(): array
    {
        return [
            'admin_deposit_permission' => 'boolean',
        ];
    }

    public function allowsAdminDeposit(): bool
    {
        return $this->admin_deposit_permission === true;
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function nominee()
    {
        return $this->hasOne(Nominee::class);
    }

    public function shares()
    {
        return $this->hasMany(Share::class);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function groupOpinion()
    {
        return $this->hasOne(GroupOpinion::class);
    }

    public function monthlyPayments()
    {
        return $this->hasMany(MonthlyPayment::class);
    }

    public function shareChangeRequests()
    {
        return $this->hasMany(ShareChangeRequest::class);
    }

    public function pendingShareChangeRequest()
    {
        return $this->hasOne(ShareChangeRequest::class)->where('status', 'pending')->latest();
    }

    public function getLatestShareAttribute()
    {
        return $this->shares()->latest()->first();
    }

    public function getTotalSharesAttribute()
    {
        return $this->shares()->sum('number_of_shares');
    }

    public function getTotalAmountAttribute()
    {
        return $this->shares()->sum('total_amount');
    }

    public function getTotalDepositedAttribute()
    {
        return $this->deposits()->sum('amount');
    }

    public function getBalanceDueAttribute()
    {
        return $this->total_amount - $this->total_deposited;
    }

    public function getPaymentStatusAttribute(): string
    {
        $totalAmount = $this->total_amount;
        $totalDeposited = $this->total_deposited;

        if ($totalAmount <= 0) return 'pending';
        if ($totalDeposited >= $totalAmount) return 'paid';
        if ($totalDeposited > 0) return 'partial';
        return 'pending';
    }
}
