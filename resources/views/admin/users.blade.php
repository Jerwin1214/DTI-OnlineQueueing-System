@extends('layouts.admin')

@section('content')
<div class="flex h-screen bg-blue-900/90 p-6">

    <!-- USER MANAGEMENT PANEL -->
    <div class="flex-1 bg-white/10 backdrop-blur-md rounded-2xl shadow-lg p-6 overflow-auto border border-white/20">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6">
            <h1 class="text-4xl font-bold text-yellow-400 mb-4 md:mb-0">
                User Management
            </h1>

            <a href="{{ route('admin.createUserForm') }}"
               class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-2xl font-semibold shadow-lg transition-colors duration-300">
               Create New User
            </a>
        </div>

        <!-- SUCCESS MESSAGE -->
        @if(session('success'))
            <div class="bg-green-200 text-green-900 p-4 rounded-lg mb-4 shadow-inner">
                {{ session('success') }}
            </div>
        @endif

        <!-- TABLE -->
        <div class="overflow-x-auto rounded-lg shadow-sm border border-white/20">
            <table class="min-w-full table-auto text-gray-100">
                <thead class="bg-blue-800/80 text-left text-yellow-400 uppercase text-sm tracking-wider">
                    <tr>
                        <th class="px-6 py-3">User ID</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Counter</th>
                        <th class="px-6 py-3">Created At</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white/10 backdrop-blur-md">
                    @forelse($users as $user)
                        <tr class="border-b border-white/20 hover:bg-blue-700/50 transition-colors duration-200">
                            <td class="px-6 py-3 font-medium">{{ $user->user_id }}</td>
                            <td class="px-6 py-3">{{ ucfirst($user->role) }}</td>
                            <td class="px-6 py-3">{{ $user->counter_id ?? '-' }}</td>
                            <td class="px-6 py-3">{{ $user->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-6 py-3 space-x-2">
                                <!-- Edit Button -->
                                <a href="{{ route('admin.editUser', $user->id) }}"
                                   class="bg-yellow-500 hover:bg-yellow-600 px-3 py-1 rounded font-semibold text-black transition">
                                    Edit
                                </a>
                                <!-- Delete Button -->
                                <form action="{{ route('admin.deleteUser', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded font-semibold text-white transition">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-300 italic">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection
