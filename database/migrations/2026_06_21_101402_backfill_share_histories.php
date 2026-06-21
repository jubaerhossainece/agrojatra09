<?php

use App\Models\Member;
use App\Models\ShareHistory;
use App\Services\MonthlyPaymentService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * One-time baseline: give every existing member a share_history row at
     * their current share count, effective from the group's start. There's
     * no real prior history to recover (past changes just overwrote the
     * share row in place), so this is the honest starting point.
     */
    public function up(): void
    {
        Member::with('shares')->get()->each(function (Member $member) {
            if ($member->total_shares <= 0) {
                return;
            }

            ShareHistory::create([
                'member_id'       => $member->id,
                'number_of_shares' => $member->total_shares,
                'total_amount'    => $member->total_amount,
                'effective_year'  => MonthlyPaymentService::GROUP_START_YEAR,
                'effective_month' => MonthlyPaymentService::GROUP_START_MONTH,
            ]);
        });
    }

    public function down(): void
    {
        ShareHistory::whereNull('share_change_request_id')->delete();
    }
};
