<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PositionPermission extends Model
{
    protected $fillable = ['position', 'permission'];

    const POSITIONS = ['president', 'secretary', 'accountant'];

    const PERMISSIONS = [
        'approve_deposits' => 'Approve / Reject Deposits',
        'delete_deposits'  => 'Delete Deposits',
    ];
}
