@extends('layouts.admin')

@section('title', 'Admin Ticket Management')

@section('content')
<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-60 bg-gray-800 text-white flex flex-col p-6">
        <!-- Logos -->
        <div class="flex justify-center mb-6 space-x-2">
            <img src="{{ asset('logoDTI.png') }}" class="h-20" alt="DTI Logo">
            <img src="{{ asset('bagongpilipinas2.png') }}" class="h-20" alt="Bagong Pilipinas Logo">
        </div>

        <!-- Menu Buttons -->
        <nav class="flex flex-col space-y-4 mt-4">
            <a href="{{ route('admin.dashboard') }}" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded text-center font-semibold">User Management</a>
            <a href="{{ route('admin.tickets.pdf') }}" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded text-center font-semibold">PDF Generator</a>

            <!-- Clear All Tickets Form -->
            <form method="POST" action="{{ route('admin.tickets.clear') }}">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 w-full px-4 py-2 rounded font-semibold">Clear All Tickets</button>
            </form>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 bg-blue-500 p-8 overflow-auto relative">

        <!-- Page Title -->
        <h1 class="text-3xl font-bold text-white mb-6">Admin Ticket Management</h1>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Ticket Creation Panel -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Generate Tickets</h2>

                <form method="POST" action="{{ route('admin.tickets.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="ticket_count" class="block text-gray-700 font-semibold mb-1">Number of Tickets</label>
                        <input type="number" name="ticket_count" id="ticket_count" min="1" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-1">Select Counters</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($counters as $counter)
                                <label class="inline-flex items-center bg-gray-100 px-3 py-2 rounded cursor-pointer hover:bg-gray-200">
                                    <input type="checkbox" name="counters[]" value="{{ $counter->id }}" class="mr-2">
                                    {{ $counter->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-semibold">Add Tickets</button>
                        <button type="submit" formaction="{{ route('admin.tickets.delete') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-semibold">Delete Selected Ticket</button>
                    </div>
                </form>
            </div>

            <!-- Tickets Table with Watermark -->
            <div class="bg-white rounded-lg shadow-lg p-6 relative">
                <!-- Watermark -->
                <img src="{{ asset('DTICPOLOGOQUEUEING.png') }}" class="absolute inset-0 m-auto opacity-20 w-1/2 h-1/2 pointer-events-none" alt="Watermark">

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Counter</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($tickets as $ticket)
                        <tr class="hover:bg-blue-50 cursor-pointer">
                            <td class="px-6 py-4">{{ $ticket->id }}</td>
                            <td class="px-6 py-4">{{ $ticket->counter->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ ucfirst($ticket->status) }}</td>
                            <td class="px-6 py-4">{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $tickets->links() }}
                </div>
            </div>

        </div>
    </main>
</div>
@endsection
