<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white font-sans">

<div class="flex h-screen">

    <!-- ================= SIDEBAR ================= -->
    @unless(isset($hideSidebar) && $hideSidebar)
    <aside class="w-64 bg-gray-800 flex flex-col">

        <!-- LOGO -->
        <div class="h-20 flex items-center justify-center border-b border-gray-700">
            <h1 class="text-xl font-bold">DTIR2 Queueing</h1>
        </div>

        <!-- MENU -->
        <nav class="flex-1 overflow-auto">
            <ul class="p-4 space-y-2">

                <!-- DASHBOARD -->
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                       class="block py-2 px-4 rounded transition
                       {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                        Dashboard
                    </a>
                </li>

                <!-- USER MANAGEMENT -->
                <li>
                    <a href="{{ route('admin.users') }}"
                       class="block py-2 px-4 rounded transition
                       {{ request()->routeIs('admin.users') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                        User Management
                    </a>
                </li>

                <!-- TICKET MANAGEMENT -->
                <li>
                    <a href="{{ route('admin.ticket.management') }}"
                       class="block py-2 px-4 rounded transition
                       {{ request()->routeIs('admin.ticket.*') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                        Ticket Management
                    </a>
                </li>

                <!-- LOGOUT (FIXED - POST METHOD) -->
                <li>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left py-2 px-4 rounded bg-red-500 hover:bg-red-600 transition">
                            Logout
                        </button>
                    </form>
                </li>

            </ul>
        </nav>

    </aside>
    @endunless
    <!-- ================= END SIDEBAR ================= -->


    <!-- ================= MAIN CONTENT ================= -->
    <div class="flex-1 flex flex-col overflow-auto">

        <!-- TOP NAVBAR -->
        @unless(isset($hideTopbar) && $hideTopbar)
        <header class="h-16 bg-gray-800 flex items-center justify-between px-6 border-b border-gray-700">

            <div></div>

            <div class="flex items-center space-x-4">
                <span class="font-medium">
                    {{ optional(Auth::user())->full_name ?? optional(Auth::user())->user_id }}
                </span>
                <span class="text-gray-400">
                    ({{ optional(Auth::user())->role }})
                </span>
            </div>

        </header>
        @endunless
        <!-- ================= END TOPBAR -->


        <!-- PAGE CONTENT -->
        <main class="flex-1 p-6 overflow-auto">
            @yield('content')
        </main>

    </div>
    <!-- ================= END MAIN CONTENT -->

</div>

<!-- ================= SCRIPTS ================= -->
@yield('scripts')

</body>
</html>
