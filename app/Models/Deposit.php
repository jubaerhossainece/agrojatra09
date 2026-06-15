<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        'attachment',
        'recorded_by',
    ];

    public function attachmentUrl(): ?string
    {
        return $this->attachment ? Storage::url($this->attachment) : null;
    }

    public function attachmentIsImage(): bool
    {
        return $this->attachment && preg_match('/\.(jpg|jpeg|png)$/i', $this->attachment);
    }

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
