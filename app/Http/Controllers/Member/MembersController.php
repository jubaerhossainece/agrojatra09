<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;

class MembersController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::with(['shares', 'deposits'])->orderBy('full_name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        $members = $query->paginate(12)->withQueryString();

        return view('member.members.index', compact('members'));
    }

    public function show(Member $member)
    {
        $member->load(['nominee', 'shares', 'deposits']);

        return view('member.members.show', compact('member'));
    }
}
