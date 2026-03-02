@extends('layouts.admin')

@section('title', 'Admin Ticket Management')

@section('content')

<div class="flex flex-col md:flex-row gap-6">

    <!-- LEFT PANEL: Ticket Generator -->
    <div class="w-full md:w-80 bg-gray-800 text-white rounded-2xl shadow-lg p-6 flex-shrink-0">

        <h2 class="text-xl font-bold mb-4 border-b border-gray-700 pb-2">
            Generate Tickets
        </h2>

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
            <div class="bg-green-500 text-white p-2 rounded mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- VALIDATION ERRORS --}}
        @if($errors->any())
            <div class="bg-red-500 text-white p-2 rounded mb-4 text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.ticket.add') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Ticket Count -->
            <div>
                <label class="block mb-1 font-medium text-gray-200">
                    Number of Tickets
                </label>
                <input type="number"
                       name="ticket_count"
                       min="1"
                       required
                       class="w-full rounded-lg p-2 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <!-- Counter Selection -->
            <div>
                <label class="block mb-1 font-medium text-gray-200">
                    Select Counters
                </label>

                <div class="flex flex-col space-y-2">
                    @for($i = 1; $i <= 5; $i++)
                        <label class="inline-flex items-center space-x-2 text-gray-200">
                            <input type="checkbox"
                                   name="counters[]"
                                   value="{{ $i }}"
                                   class="form-checkbox h-4 w-4 text-blue-500 rounded">
                            <span>Counter {{ $i }}</span>
                        </label>
                    @endfor
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition">
                Add Tickets
            </button>
        </form>

        <!-- CLEAR ALL TICKETS -->
        <form action="{{ route('admin.ticket.clear') }}" method="POST" class="mt-4">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg transition">
                Clear All Tickets
            </button>
        </form>

    </div>

    <!-- RIGHT PANEL: Ticket Table -->
    <div class="flex-1 bg-gray-50 rounded-2xl shadow-lg p-6 relative">

        <!-- WATERMARK -->
        <img src="{{ asset('images/DTICPOLOGOQUEUEING.png') }}"
             class="absolute inset-0 m-auto opacity-10 pointer-events-none"
             style="max-height:300px;">

        <h1 class="text-2xl font-bold mb-6 text-gray-800">
            Admin Ticket Management
        </h1>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse shadow-sm rounded-lg overflow-hidden">
                <thead class="bg-gray-200 text-gray-700 uppercase text-sm">
                    <tr>
                        <th class="p-3 text-left">ID</th>
                        <th class="p-3 text-left">Ticket Number</th>
                        <th class="p-3 text-left">Counter</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left">Action</th>
                    </tr>
                </thead>

                <tbody class="text-gray-700 text-sm">
                    @forelse($tickets as $ticket)
                        <tr class="border-b hover:bg-gray-100 transition">

                            <!-- ID -->
                            <td class="p-3">{{ $ticket->id }}</td>

                            <!-- Formatted Ticket Number -->
                            <td class="p-3 font-medium">
                                {{ 'C' . str_pad($ticket->ticket_number, 3, '0', STR_PAD_LEFT) }}
                            </td>

                            <!-- Counter -->
                            <td class="p-3">
                                @if($ticket->counter_id)
                                    Counter {{ $ticket->counter_id }}
                                @else
                                    <span class="text-gray-400 italic">Not Assigned</span>
                                @endif
                            </td>

                            <!-- UPDATED STATUS SYSTEM -->
                            <td class="p-3">
                                @if($ticket->status === 'waiting')
                                    <span class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full text-xs">
                                        Waiting
                                    </span>

                                @elseif($ticket->status === 'serving')
                                    <span class="bg-blue-200 text-blue-800 px-2 py-1 rounded-full text-xs">
                                        Serving
                                    </span>

                                @elseif($ticket->status === 'done')
                                    <span class="bg-green-200 text-green-800 px-2 py-1 rounded-full text-xs">
                                        Done
                                    </span>

                                @else
                                    <span class="bg-gray-200 text-gray-800 px-2 py-1 rounded-full text-xs">
                                        {{ ucfirst($ticket->status) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Delete -->
                            <td class="p-3">
                                <form action="{{ route('admin.ticket.delete', $ticket->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Are you sure you want to delete this ticket?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition">
                                        Delete
                                    </button>
                                </form>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5"
                                class="p-3 text-center text-gray-500 italic">
                                No tickets available.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>

</div>

@endsection
