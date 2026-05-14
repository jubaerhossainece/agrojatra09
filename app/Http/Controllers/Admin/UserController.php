<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('member')->orderBy('name')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'role'     => ['required', 'in:admin,member'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $data = ['role' => $request->role];
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'User updated successfully.');
    }
}
