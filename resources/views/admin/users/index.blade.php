<x-layouts.admin title="User Management">

<div class="mt-4 space-y-4">

    <p class="text-sm text-gray-500">{{ $users->total() }} users in the system.</p>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">User</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">Email</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Role</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="font-medium text-gray-900">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600 hidden sm:table-cell">{{ $user->email }}</td>
                        <td class="px-4 py-3 text-center">
                            <x-badge :status="$user->role" />
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.update', $user) }}"
                                      class="flex items-center justify-end gap-2"
                                      onsubmit="return confirm('Update this user?')">
                                    @csrf @method('PUT')
                                    <select name="role"
                                            class="border border-gray-300 rounded-lg px-2 py-1 text-xs focus:ring-1 focus:ring-green-500 outline-none">
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="member" {{ $user->role === 'member' ? 'selected' : '' }}>Member</option>
                                    </select>
                                    <input type="text" name="password" placeholder="New password (optional)"
                                           class="border border-gray-300 rounded-lg px-2 py-1 text-xs focus:ring-1 focus:ring-green-500 outline-none w-40 hidden md:block">
                                    <button type="submit"
                                            class="bg-green-700 hover:bg-green-800 text-white px-3 py-1 rounded-lg text-xs font-medium transition-colors">
                                        Save
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400 italic">Current user</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($users->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

</x-layouts.admin>
