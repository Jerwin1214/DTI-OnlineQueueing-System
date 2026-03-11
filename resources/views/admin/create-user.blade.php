@extends('layouts.admin')

@section('content')

<div class="flex items-center justify-center min-h-screen bg-blue-900/90 p-4">

    <!-- CREATE USER CARD -->
    <div class="w-full max-w-md bg-white/10 backdrop-blur-md rounded-3xl shadow-2xl p-6 md:p-8 border border-white/20 max-h-[95vh] overflow-y-auto">

        <!-- HEADER -->
        <h1 class="text-3xl md:text-4xl font-extrabold text-yellow-400 mb-6 text-center tracking-wide">
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

        <form method="POST" action="{{ route('admin.storeUser') }}" class="space-y-4" autocomplete="off">
        @csrf

        <!-- USERNAME -->
        <div>
            <label for="user_id" class="block text-white font-semibold mb-1">Username</label>
            <input type="text"
                name="user_id"
                id="user_id"
                value="{{ old('user_id') }}"
                required
                class="w-full px-4 py-3 rounded-2xl border border-white/30 bg-white/20 text-white placeholder-white focus:outline-none focus:ring-2 focus:ring-yellow-400 transition">
        </div>

        <!-- PASSWORD -->
        <div class="relative">
            <label for="password" class="block text-white font-semibold mb-1">Password</label>

            <input type="password"
                name="password"
                id="password"
                required
                pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$"
                title="Password must be at least 8 characters and include uppercase, lowercase, number, and special character"
                class="w-full px-4 py-3 pr-10 rounded-2xl border border-white/30 bg-white/20 text-white placeholder-white focus:outline-none focus:ring-2 focus:ring-yellow-400 transition">

            <button type="button"
                onclick="togglePassword('password')"
                class="absolute right-3 top-10 text-white/70 hover:text-yellow-400 transition text-lg">
                👁
            </button>

            <p class="text-yellow-400 text-xs mt-1">
                Password must be 8+ characters with uppercase, lowercase, number & special character
            </p>
        </div>

        <!-- CONFIRM PASSWORD -->
        <div class="relative">
            <label for="password_confirmation" class="block text-white font-semibold mb-1">Confirm Password</label>

            <input type="password"
                name="password_confirmation"
                id="password_confirmation"
                required
                class="w-full px-4 py-3 pr-10 rounded-2xl border border-white/30 bg-white/20 text-white placeholder-white focus:outline-none focus:ring-2 focus:ring-yellow-400 transition">

            <button type="button"
                onclick="togglePassword('password_confirmation')"
                class="absolute right-3 top-10 text-white/70 hover:text-yellow-400 transition text-lg">
                👁
            </button>

            <p id="matchMessage" class="text-xs mt-1"></p>
        </div>

        <!-- FULL NAME -->
        <div>
            <label for="full_name" class="block text-white font-semibold mb-1">Full Name</label>
            <input type="text"
                name="full_name"
                id="full_name"
                value="{{ old('full_name') }}"
                required
                class="w-full px-4 py-3 rounded-2xl border border-white/30 bg-white/20 text-white placeholder-white focus:outline-none focus:ring-2 focus:ring-yellow-400 transition">
        </div>

        <!-- ROLE -->
        <div>
            <label for="role" class="block text-white font-semibold mb-1">Role</label>
            <select name="role"
                id="role"
                required
                class="w-full px-4 py-3 rounded-2xl border border-white/30 bg-white/20 text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 transition">

                <option value="" class="text-black">Select Role</option>
                <option value="admin" class="text-black" {{ old('role')=='admin' ? 'selected' : '' }}>Administrator</option>
                <option value="counter" class="text-black" {{ old('role')=='counter' ? 'selected' : '' }}>Counter</option>
            </select>
        </div>

        <!-- COUNTER -->
        <div>
            <label for="counter_id" class="block text-white font-semibold mb-1">Assign Counter</label>
            <input type="number"
                name="counter_id"
                id="counter_id"
                value="{{ old('counter_id') }}"
                placeholder="Enter counter number if applicable"
                class="w-full px-4 py-3 rounded-2xl border border-white/30 bg-white/20 text-white placeholder-white focus:outline-none focus:ring-2 focus:ring-yellow-400 transition">
        </div>

        <!-- BUTTONS -->
        <div class="flex justify-end gap-3 pt-4">

            <a href="{{ route('admin.users') }}"
                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-2xl font-semibold shadow-lg transition">
                Cancel
            </a>

            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-2xl font-semibold shadow-lg transition">
                Save User
            </button>

        </div>

        </form>

    </div>

</div>

<script>

function togglePassword(id){
    const field = document.getElementById(id);
    field.type = field.type === 'password' ? 'text' : 'password';
}

/* PASSWORD MATCH VALIDATION */

const password = document.getElementById('password');
const confirmPassword = document.getElementById('password_confirmation');
const matchMessage = document.getElementById('matchMessage');

confirmPassword.addEventListener('keyup', function(){

    if(confirmPassword.value === ""){
        matchMessage.textContent = "";
        return;
    }

    if(password.value === confirmPassword.value){
        matchMessage.textContent = "Passwords match ✓";
        matchMessage.classList.remove("text-red-400");
        matchMessage.classList.add("text-green-400");
    }else{
        matchMessage.textContent = "Passwords do not match";
        matchMessage.classList.remove("text-green-400");
        matchMessage.classList.add("text-red-400");
    }

});

</script>

@endsection
