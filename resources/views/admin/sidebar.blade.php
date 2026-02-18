<div class="w-64 bg-gray-800 text-white h-screen p-6 flex flex-col">

    <!-- LOGO / TITLE -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-center text-blue-400">
            Admin Panel
        </h1>
    </div>

    <!-- NAVIGATION LINKS -->
    <nav class="flex-1">
        <ul class="space-y-3">

            <li>
                <a href="{{ route('admin.dashboard') }}"
                   class="block py-2 px-4 rounded hover:bg-blue-600 transition">
                    Dashboard
                </a>
            </li>

            <li>
                <a href="{{ route('admin.users') }}"
                   class="block py-2 px-4 rounded hover:bg-blue-600 transition">
                    Manage Users
                </a>
            </li>

            <li>
                <a href="{{ route('admin.displayScreen') }}"
                   class="block py-2 px-4 rounded hover:bg-blue-600 transition">
                    Display Screen
                </a>
            </li>

            <li>
                <a href="{{ route('admin.logout') }}"
                   class="block py-2 px-4 rounded hover:bg-red-600 transition">
                    Logout
                </a>
            </li>

        </ul>
    </nav>

    <!-- FOOTER / INFO -->
    <div class="mt-auto text-center text-gray-400 text-sm">
        &copy; {{ date('Y') }} Your Company
    </div>

</div>
ww
