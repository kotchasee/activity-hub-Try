<x-app-layout>

    <div class="py-10 bg-orange-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <h1 class="text-3xl font-bold text-orange-600 mb-6">
                Manage Users
            </h1>

            @if(session('success'))
                <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 text-red-800 px-4 py-3 rounded-lg mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-md border border-orange-100 overflow-hidden">
                <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left">
                    <thead class="bg-orange-500 text-white">
                        <tr>
                            <th class="px-6 py-3">ID</th>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Current Role</th>
                            <th class="px-6 py-3">Change Role</th>
                            <th class="px-6 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr class="border-b border-orange-50 hover:bg-orange-25">
                            <td class="px-6 py-4 text-gray-700">{{ $user->id }}</td>
                            <td class="px-6 py-4 text-gray-800 font-semibold">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold
                                    @if($user->role === 'admin') bg-red-100 text-red-700
                                    @elseif($user->role === 'staff') bg-blue-100 text-blue-700
                                    @elseif($user->role === 'admin_club') bg-purple-100 text-purple-700
                                    @else bg-gray-100 text-gray-700
                                    @endif
                                ">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.updateRole', $user->id) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" class="rounded-lg border-orange-300 text-sm focus:ring-orange-500 focus:border-orange-500">
                                        <option value="student" {{ $user->role === 'student' ? 'selected' : '' }}>Student</option>
                                        <option value="admin_club" {{ $user->role === 'admin_club' ? 'selected' : '' }}>Admin Club</option>
                                        <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>Staff</option>
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                            </td>
                            <td class="px-6 py-4">
                                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm transition">
                                        Save
                                    </button>
                                </form>
                                @else
                                    <span class="text-gray-400 text-sm italic">— (You)</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-gray-400 text-sm italic">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>
