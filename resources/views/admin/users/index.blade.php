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
                                      class="flex items-center justify-end gap-2">
                                    @csrf @method('PUT')
                                    <div x-data="{ open: false, value: '{{ $user->role }}', label: '{{ ucfirst($user->role) }}' }"
                                         @click.outside="open = false" class="relative">
                                        <input type="hidden" name="role" :value="value">
                                        <button type="button" @click="open = !open"
                                                class="flex items-center gap-1.5 bg-white border border-gray-300 rounded-lg px-2.5 py-1 text-xs text-gray-700 hover:border-green-400 focus:outline-none focus:ring-1 focus:ring-green-500 transition-colors cursor-pointer w-24 justify-between">
                                            <span x-text="label"></span>
                                            <svg class="w-3 h-3 text-gray-400 flex-shrink-0 transition-transform duration-150" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        <div x-show="open"
                                             x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                             x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                             class="absolute right-0 z-50 mt-1 w-28 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                                            <div class="py-1">
                                                <button type="button" @click="value = 'admin'; label = 'Admin'; open = false"
                                                        :class="{ 'bg-green-50 text-green-700 font-semibold': value === 'admin' }"
                                                        class="w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors">Admin</button>
                                                <button type="button" @click="value = 'member'; label = 'Member'; open = false"
                                                        :class="{ 'bg-green-50 text-green-700 font-semibold': value === 'member' }"
                                                        class="w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors">Member</button>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="text" name="password" placeholder="New password (optional)"
                                           class="border border-gray-300 rounded-lg px-2 py-1 text-xs focus:ring-1 focus:ring-green-500 outline-none w-40 hidden md:block">
                                    <button x-data type="button"
                                            @click="$dispatch('open-confirm', {
                                                title: 'Update User',
                                                message: 'Save the new role for this user?',
                                                confirmLabel: 'Save Changes',
                                                confirmClass: 'bg-green-700 hover:bg-green-800',
                                                target: $el.closest('form')
                                            })"
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
