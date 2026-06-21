<x-layouts.admin title="User Management">

<div class="mt-4 space-y-4">

    <p class="text-sm text-gray-500">{{ $users->total() }} users in the system.</p>

    {{-- Search --}}
    <form method="GET" class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by name or email..."
               x-data
               x-init="if ($el.value) { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
               x-on:input.debounce.500ms="$el.form.submit()"
               class="flex-1 min-w-[200px] border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
        <button type="submit"
                class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            Search
        </button>
        @if(request()->hasAny(['search']))
            <a href="{{ route('admin.users.index') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Clear
            </a>
        @endif
    </form>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">User</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">Email</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Position</th>
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
                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                @if($user->position)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                        {{ $user->positionLabel() }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                        Member
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.update', $user) }}"
                                      x-data="{
                                          position: '{{ $user->position ?? '' }}',
                                          posOpen: false,
                                          posTop: '0px',
                                          posLeft: '0px'
                                      }"
                                      @click.outside="posOpen = false"
                                      class="flex items-center justify-end gap-2 flex-wrap">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="position" :value="position">

                                    {{-- Position dropdown — editable by president only --}}
                                    @if(auth()->user()->isPresident())
                                        <div @click.outside="posOpen = false" class="relative">
                                            <button type="button"
                                                    @click="
                                                        posOpen = !posOpen;
                                                        if (posOpen) {
                                                            const r = $el.getBoundingClientRect();
                                                            posTop = (r.bottom + 4) + 'px';
                                                            posLeft = (r.right - 128) + 'px';
                                                        }
                                                    "
                                                    class="flex items-center gap-1.5 bg-white border border-gray-300 rounded-lg px-2.5 py-1 text-xs text-gray-700 hover:border-purple-400 focus:outline-none focus:ring-1 focus:ring-purple-500 transition-colors cursor-pointer w-28 justify-between">
                                                <span x-text="position ? position.charAt(0).toUpperCase() + position.slice(1) : 'None'"></span>
                                                <svg class="w-3 h-3 text-gray-400 flex-shrink-0" :class="{ 'rotate-180': posOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>
                                            <div x-show="posOpen"
                                                 :style="{ top: posTop, left: posLeft }"
                                                 style="display:none"
                                                 x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                                 x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                                 class="fixed z-50 w-32 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                                                <div class="py-1">
                                                    <button type="button" @click="position = ''; posOpen = false"
                                                            :class="{ 'bg-gray-100 text-gray-700 font-semibold': position === '' }"
                                                            class="w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 transition-colors">None</button>
                                                    <button type="button" @click="position = 'president'; posOpen = false"
                                                            :class="{ 'bg-purple-50 text-purple-700 font-semibold': position === 'president' }"
                                                            class="w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors">President</button>
                                                    <button type="button" @click="position = 'secretary'; posOpen = false"
                                                            :class="{ 'bg-purple-50 text-purple-700 font-semibold': position === 'secretary' }"
                                                            class="w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors">Secretary</button>
                                                    <button type="button" @click="position = 'accountant'; posOpen = false"
                                                            :class="{ 'bg-purple-50 text-purple-700 font-semibold': position === 'accountant' }"
                                                            class="w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors">Accountant</button>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center justify-center w-28 px-2.5 py-1 text-xs text-gray-500 bg-gray-50 border border-gray-200 rounded-lg" title="Only the president can change a user's position">
                                            {{ $user->position ? ucfirst($user->position) : 'None' }}
                                        </span>
                                    @endif

                                    <input type="text" name="password" placeholder="New password"
                                           class="border border-gray-300 rounded-lg px-2 py-1 text-xs focus:ring-1 focus:ring-green-500 outline-none w-36 hidden md:block">

                                    <button x-data type="button"
                                            @click="$dispatch('open-confirm', {
                                                title: 'Update User',
                                                message: 'Save the new position for this user?',
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
