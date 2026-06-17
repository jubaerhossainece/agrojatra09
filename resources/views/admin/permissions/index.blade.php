<x-layouts.admin title="Permissions">

<div class="mt-4 max-w-2xl space-y-6">

    <p class="text-sm text-gray-500">Control which admin positions can perform specific actions. Changes take effect immediately.</p>

    <form method="POST" action="{{ route('admin.permissions.update') }}">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase w-1/2">Permission</th>
                        @foreach($positions as $position)
                            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase">
                                {{ ucfirst($position) }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($permissions as $key => $label)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-4 font-medium text-gray-800">{{ $label }}</td>
                            @foreach($positions as $position)
                                <td class="px-5 py-4 text-center">
                                    <input type="checkbox"
                                           name="{{ $position }}_{{ $key }}"
                                           value="1"
                                           {{ in_array($key, $assigned[$position] ?? []) ? 'checked' : '' }}
                                           class="w-4 h-4 rounded border-gray-300 text-green-700 focus:ring-green-500 cursor-pointer">
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-end mt-4">
            <button type="submit"
                    class="bg-green-700 hover:bg-green-800 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                Save Permissions
            </button>
        </div>
    </form>

</div>

</x-layouts.admin>
