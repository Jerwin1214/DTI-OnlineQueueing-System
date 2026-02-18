@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-blue-900/90 p-6">

    <!-- CREATE USER CARD -->
    <div class="w-full max-w-md bg-white/10 backdrop-blur-md rounded-3xl shadow-2xl p-8 border border-white/20 overflow-auto">

        <!-- HEADER -->
        <h1 class="text-4xl font-extrabold text-yellow-400 mb-8 text-center tracking-wide">
            Create New User
        </h1>

        <!-- ERROR MESSAGES -->
        @if ($errors->any())
            <div class="bg-red-200 text-red-900 p-4 rounded-lg mb-4 shadow-inner">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- SUCCESS MESSAGE -->
        @if(session('success'))
            <div class="bg-green-200 text-green-900 p-4 rounded-lg mb-4 shadow-inner">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.storeUser') }}" class="space-y-5">
            @csrf

            <!-- USERNAME -->
            <div>
                <label for="user_id" class="block text-white font-semibold mb-1">Username</label>
                <input type="text" name="user_id" id="user_id" value="{{ old('user_id') }}" required
                       class="w-full px-4 py-3 rounded-2xl border border-white/30 bg-white/20 text-white placeholder-white focus:outline-none focus:ring-2 focus:ring-yellow-400 transition">
            </div>

            <!-- PASSWORD -->
            <div class="relative">
                <label for="password" class="block text-white font-semibold mb-1">Password</label>
                <input type="password" name="password" id="password" required
                       class="w-full px-4 py-3 rounded-2xl border border-white/30 bg-white/20 text-white placeholder-white focus:outline-none focus:ring-2 focus:ring-yellow-400 transition">
                <button type="button" onclick="togglePassword('password')"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-yellow-400 transition">👁</button>
                <p class="text-yellow-400 text-xs mt-1">
                    Password must be 8+ chars with uppercase, lowercase, number & special character
                </p>
            </div>

            <!-- CONFIRM PASSWORD -->
            <div class="relative">
                <label for="password_confirmation" class="block text-white font-semibold mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                       class="w-full px-4 py-3 rounded-2xl border border-white/30 bg-white/20 text-white placeholder-white focus:outline-none focus:ring-2 focus:ring-yellow-400 transition">
                <button type="button" onclick="togglePassword('password_confirmation')"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-yellow-400 transition">👁</button>
            </div>

            <!-- FULL NAME -->
            <div>
                <label for="full_name" class="block text-white font-semibold mb-1">Full Name</label>
                <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" required
                       class="w-full px-4 py-3 rounded-2xl border border-white/30 bg-white/20 text-white placeholder-white focus:outline-none focus:ring-2 focus:ring-yellow-400 transition">
            </div>

            <!-- ROLE -->
            <div>
                <label for="role" class="block text-white font-semibold mb-1">Role</label>
                <select name="role" id="role" required
                        class="w-full px-4 py-3 rounded-2xl border border-white/30 bg-white/20 text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 transition appearance-none">
                    <option value="">Select Role</option>
                    <option value="admin" {{ old('role')=='admin' ? 'selected' : '' }}>Administrator</option>
                    <option value="counter" {{ old('role')=='counter' ? 'selected' : '' }}>Counter</option>
                </select>
            </div>

            <!-- COUNTER -->
            <div>
                <label for="counter_id" class="block text-white font-semibold mb-1">Assign Counter</label>
                <input type="number" name="counter_id" id="counter_id" value="{{ old('counter_id') }}"
                       class="w-full px-4 py-3 rounded-2xl border border-white/30 bg-white/20 text-white placeholder-white focus:outline-none focus:ring-2 focus:ring-yellow-400 transition"
                       placeholder="Enter counter number if applicable">
            </div>

            <!-- BUTTONS -->
            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('admin.users') }}"
                   class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-2xl font-semibold shadow-lg transition">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-semibold shadow-lg transition">
                    Save User
                </button>
            </div>
        </form>
    </div>

</div>

<!-- PASSWORD TOGGLE SCRIPT -->
<script>
function togglePassword(id) {
    const field = document.getElementById(id);
    field.type = field.type === 'password' ? 'text' : 'password';
}
</script>
@endsection
