<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Staff Management') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.staff.create') }}" class="bg-[#1E3C2C] text-white px-4 py-2 rounded-lg hover:bg-[#143023] transition">
                    + Send Invitation
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">Role</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">Weekend</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($staff as $member)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4">{{ $member->name }}</td>
                                    <td class="py-3 px-4 text-sm">{{ $member->email }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium ring-1 ring-inset
                                            @if($member->isAdmin()) bg-red-50 text-red-700 ring-red-200
                                            @elseif($member->isDelivery()) bg-orange-50 text-orange-700 ring-orange-200
                                            @else bg-violet-50 text-violet-700 ring-violet-200
                                            @endif">
                                            {{ ucfirst($member->user_type) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($member->is_active && $member->approved_at)
                                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                        @elseif($member->approved_at && !$member->is_active)
                                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                                        @else
                                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($member->is_weekend_override)
                                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Override</span>
                                        @else
                                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Default</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex gap-2 flex-wrap">
                                            @if(!$member->is_active || !$member->approved_at)
                                                <form method="POST" action="{{ route('admin.staff.activate', $member) }}">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-800 text-sm">Activate</button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.staff.deactivate', $member) }}">
                                                    @csrf
                                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Deactivate</button>
                                                </form>
                                            @endif
                                            
                                            <form method="POST" action="{{ route('admin.staff.toggle-weekend-override', $member) }}">
                                                @csrf
                                                <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm">
                                                    {{ $member->is_weekend_override ? 'Remove Override' : 'Override Weekend' }}
                                                </button>
                                            </form>
                                            
                                            <a href="{{ route('admin.staff.edit', $member) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">Edit</a>
                                            
                                            <form method="POST" action="{{ route('admin.staff.destroy', $member) }}" 
                                                  onsubmit="return confirm('Delete this staff member?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-gray-500">
                                        No staff members found. Send an invitation to get started!
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>