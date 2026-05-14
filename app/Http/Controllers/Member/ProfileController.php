<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;

class ProfileController extends Controller
{
    public function show()
    {
        $member = Member::with(['nominee', 'shares', 'deposits'])
            ->findOrFail(auth()->user()->member_id);

        return view('member.profile', compact('member'));
    }
}
