<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\Member;
use App\Models\Share;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::with(['shares', 'deposits']);

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $members = $query->latest()->paginate(15)->withQueryString();

        return view('admin.members.index', compact('members'));
    }

    public function create()
    {
        return view('admin.members.create');
    }

    public function store(StoreMemberRequest $request)
    {
        $data = $request->validated();

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('members', 'public');
        }

        $member = Member::create([
            'full_name'         => $data['full_name'],
            'email'             => $data['email'],
            'phone'             => $data['phone'],
            'permanent_address' => $data['permanent_address'],
            'present_address'   => $data['present_address'],
            'profession'        => $data['profession'] ?? null,
            'company_name'      => $data['company_name'] ?? null,
            'company_address'   => $data['company_address'] ?? null,
            'blood_group'       => $data['blood_group'] ?? null,
            'photo'             => $photoPath,
            'status'            => $data['status'],
        ]);

        $member->nominee()->create([
            'nominee_name' => $data['nominee_name'],
            'relationship' => $data['relationship'],
            'mobile'       => $data['nominee_mobile'],
            'address'      => $data['nominee_address'],
        ]);

        $shares = (int) $data['number_of_shares'];
        $share = $member->shares()->create([
            'number_of_shares' => $shares,
            'total_amount'     => $shares * 2000,
            'status'           => 'pending',
        ]);

        User::create([
            'name'      => $member->full_name,
            'email'     => $member->email,
            'password'  => Hash::make('agrojatra09'),
            'role'      => 'member',
            'member_id' => $member->id,
        ]);

        return redirect()->route('admin.members.show', $member)
            ->with('success', 'Member added successfully.');
    }

    public function show(Member $member)
    {
        $member->load(['nominee', 'shares', 'deposits.recorder', 'groupOpinion']);
        $pendingShareChange   = $member->shareChangeRequests()->where('status', 'pending')->latest()->first();
        $shareChangeHistory   = $member->shareChangeRequests()->with('requestedBy')->latest()->get();
        return view('admin.members.show', compact('member', 'pendingShareChange', 'shareChangeHistory'));
    }

    public function edit(Member $member)
    {
        $member->load(['nominee', 'shares']);
        return view('admin.members.edit', compact('member'));
    }

    public function update(UpdateMemberRequest $request, Member $member)
    {
        $data = $request->validated();

        $photoPath = $member->photo;
        if ($request->hasFile('photo')) {
            if ($member->photo) {
                Storage::disk('public')->delete($member->photo);
            }
            $photoPath = $request->file('photo')->store('members', 'public');
        }

        $member->update([
            'full_name'         => $data['full_name'],
            'email'             => $data['email'],
            'phone'             => $data['phone'],
            'permanent_address' => $data['permanent_address'],
            'present_address'   => $data['present_address'],
            'profession'        => $data['profession'] ?? null,
            'company_name'      => $data['company_name'] ?? null,
            'company_address'   => $data['company_address'] ?? null,
            'blood_group'       => $data['blood_group'] ?? null,
            'photo'             => $photoPath,
            'status'            => $data['status'],
        ]);

        if ($member->nominee) {
            $member->nominee->update([
                'nominee_name' => $data['nominee_name'],
                'relationship' => $data['relationship'],
                'mobile'       => $data['nominee_mobile'],
                'address'      => $data['nominee_address'],
            ]);
        } else {
            $member->nominee()->create([
                'nominee_name' => $data['nominee_name'],
                'relationship' => $data['relationship'],
                'mobile'       => $data['nominee_mobile'],
                'address'      => $data['nominee_address'],
            ]);
        }

        $user = User::where('member_id', $member->id)->first();
        if ($user) {
            $user->update(['name' => $member->full_name, 'email' => $member->email]);
        }

        return redirect()->route('admin.members.show', $member)
            ->with('success', 'Member updated successfully.');
    }

    public function destroy(Member $member)
    {
        if ($member->photo) {
            Storage::disk('public')->delete($member->photo);
        }
        $member->delete();

        return redirect()->route('admin.members.index')
            ->with('success', 'Member deleted successfully.');
    }
}
