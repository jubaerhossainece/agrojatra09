<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;

class MembersController extends Controller
{
    public function index()
    {
        $members = Member::with(['shares', 'deposits'])
            ->orderBy('full_name')
            ->get();

        return view('member.members.index', compact('members'));
    }

    public function show(Member $member)
    {
        $member->load(['nominee', 'shares', 'deposits']);

        return view('member.members.show', compact('member'));
    }
}
