<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nominee extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'nominee_name',
        'relationship',
        'mobile',
        'address',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
