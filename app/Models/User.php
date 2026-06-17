<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'position',
        'member_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function isAdmin(): bool
    {
        return $this->position !== null;
    }

    public function isPresident(): bool
    {
        return $this->position === 'president';
    }

    public function isSecretary(): bool
    {
        return $this->position === 'secretary';
    }

    public function isAccountant(): bool
    {
        return $this->position === 'accountant';
    }

    public function hasPermission(string $permission): bool
    {
        if (!$this->position) return false;

        $perms = Cache::rememberForever("position_perms_{$this->position}", fn() =>
            PositionPermission::where('position', $this->position)->pluck('permission')->all()
        );

        return in_array($permission, $perms);
    }

    public function canApproveDeposits(): bool
    {
        return $this->hasPermission('approve_deposits');
    }

    public function canDeleteDeposits(): bool
    {
        return $this->hasPermission('delete_deposits');
    }

    public function positionLabel(): string
    {
        return match ($this->position) {
            'president'  => 'President',
            'secretary'  => 'Secretary',
            'accountant' => 'Accountant',
            default      => 'Administrator',
        };
    }
}
