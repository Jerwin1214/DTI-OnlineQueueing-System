<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Queue System Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script> <!-- for styling -->
</head>
<body class="bg-[#1e1e2f] flex items-center justify-center h-screen">
    <div class="w-[400px] p-8 rounded-lg shadow-lg bg-[#1e1e2f] text-white">
        <h1 class="text-2xl font-bold text-center mb-6">Queue System Login</h1>

        @if ($errors->any())
            <div class="bg-red-600 text-white p-2 rounded mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-4">
                <input type="text" name="username" placeholder="Username"
                    class="w-full p-3 rounded bg-[#2c2c3d] text-white placeholder-gray-400 focus:outline-none" />
            </div>

            <div class="mb-4">
                <input type="password" name="password" placeholder="Password"
                    class="w-full p-3 rounded bg-[#2c2c3d] text-white placeholder-gray-400 focus:outline-none" />
            </div>

            <button type="submit"
                class="w-full py-2 bg-blue-900 hover:bg-blue-800 font-bold rounded shadow">Login</button>
        </form>
    </div>
</body>
</html>
