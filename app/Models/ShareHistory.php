<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShareHistory extends Model
{
    protected $fillable = [
        'member_id', 'number_of_shares', 'total_amount',
        'effective_year', 'effective_month', 'share_change_request_id',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function shareChangeRequest()
    {
        return $this->belongsTo(ShareChangeRequest::class);
    }

    /**
     * The share history row in effect for a member at a given year/month —
     * the most recent row at or before that period. Ties within the same
     * period (multiple changes in one month) resolve to the latest one.
     */
    public static function asOf(int $memberId, int $year, int $month): ?self
    {
        return static::where('member_id', $memberId)
            ->where(function ($q) use ($year, $month) {
                $q->where('effective_year', '<', $year)
                    ->orWhere(function ($q2) use ($year, $month) {
                        $q2->where('effective_year', $year)->where('effective_month', '<=', $month);
                    });
            })
            ->orderByDesc('effective_year')
            ->orderByDesc('effective_month')
            ->orderByDesc('id')
            ->first();
    }
}
