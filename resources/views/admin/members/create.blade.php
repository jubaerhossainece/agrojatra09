<x-layouts.admin title="Add Member">

<div class="mt-4 max-w-4xl">

    <div class="mb-4">
        <a href="{{ route('admin.members.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Members</a>
    </div>

    <form method="POST" action="{{ route('admin.members.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Personal Info --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="font-semibold text-gray-800 mb-4">Personal Information</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none @error('full_name') border-red-400 @enderror">
                    @error('full_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none @error('email') border-red-400 @enderror">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Blood Group</label>
                    @php $initBg = old('blood_group', ''); @endphp
                    <div x-data="{ open: false, value: '{{ $initBg }}', label: '{{ $initBg ?: 'Select' }}' }"
                         @click.outside="open = false" class="relative">
                        <input type="hidden" name="blood_group" :value="value">
                        <button type="button" @click="open = !open"
                                class="w-full flex items-center justify-between gap-2 bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm text-left text-gray-700 hover:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors cursor-pointer">
                            <span x-text="label" class="truncate"></span>
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform duration-150" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                             class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                            <div class="py-1">
                                <button type="button" @click="value = ''; label = 'Select'; open = false"
                                        :class="{ 'bg-green-50 text-green-700 font-semibold': value === '' }"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors">Select</button>
                                @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                    <button type="button" @click="value = '{{ $bg }}'; label = '{{ $bg }}'; open = false"
                                            :class="{ 'bg-green-50 text-green-700 font-semibold': value === '{{ $bg }}' }"
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors">{{ $bg }}</button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    @php $initStatus = old('status', 'active'); $initStatusLabel = $initStatus === 'active' ? 'Active' : 'Inactive'; @endphp
                    <div x-data="{ open: false, value: '{{ $initStatus }}', label: '{{ $initStatusLabel }}' }"
                         @click.outside="open = false" class="relative">
                        <input type="hidden" name="status" :value="value">
                        <button type="button" @click="open = !open"
                                class="w-full flex items-center justify-between gap-2 bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm text-left text-gray-700 hover:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors cursor-pointer">
                            <span x-text="label" class="truncate"></span>
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform duration-150" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                             class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                            <div class="py-1">
                                <button type="button" @click="value = 'active'; label = 'Active'; open = false"
                                        :class="{ 'bg-green-50 text-green-700 font-semibold': value === 'active' }"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors">Active</button>
                                <button type="button" @click="value = 'inactive'; label = 'Inactive'; open = false"
                                        :class="{ 'bg-green-50 text-green-700 font-semibold': value === 'inactive' }"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors">Inactive</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Photo</label>
                    <input type="file" name="photo" accept="image/*"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Permanent Address <span class="text-red-500">*</span></label>
                    <textarea name="permanent_address" rows="2" required
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">{{ old('permanent_address') }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Present Address <span class="text-red-500">*</span></label>
                    <textarea name="present_address" rows="2" required
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">{{ old('present_address') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Professional Info --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="font-semibold text-gray-800 mb-4">Professional Information</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profession</label>
                    <input type="text" name="profession" value="{{ old('profession') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                    <input type="text" name="company_name" value="{{ old('company_name') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Company Address</label>
                    <input type="text" name="company_address" value="{{ old('company_address') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                </div>
            </div>
        </div>

        {{-- Shares --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="font-semibold text-gray-800 mb-2">Share Investment</h2>
            <p class="text-sm text-gray-500 mb-4">1 share = BDT 2,000</p>
            <div class="flex items-center gap-4">
                <div class="flex-1 max-w-xs">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Number of Shares <span class="text-red-500">*</span></label>
                    <input type="number" name="number_of_shares" value="{{ old('number_of_shares', 1) }}" min="1" max="10" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none"
                           oninput="document.getElementById('share-total').textContent = '৳ ' + (this.value * 2000).toLocaleString()">
                </div>
                <div class="mt-5">
                    <p class="text-sm text-gray-500">Total Amount:</p>
                    <p id="share-total" class="text-xl font-bold text-green-700">৳ 2,000</p>
                </div>
            </div>
        </div>

        {{-- Nominee --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="font-semibold text-gray-800 mb-4">Nominee Information</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominee Name <span class="text-red-500">*</span></label>
                    <input type="text" name="nominee_name" value="{{ old('nominee_name') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Relationship <span class="text-red-500">*</span></label>
                    <input type="text" name="relationship" value="{{ old('relationship') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominee Mobile <span class="text-red-500">*</span></label>
                    <input type="text" name="nominee_mobile" value="{{ old('nominee_mobile') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominee Address <span class="text-red-500">*</span></label>
                    <textarea name="nominee_address" rows="2" required
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">{{ old('nominee_address') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="bg-green-700 hover:bg-green-800 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                Add Member
            </button>
            <a href="{{ route('admin.members.index') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>

</x-layouts.admin>
