@extends('layouts.admin')

@section('content')
<div class="flex flex-col items-center justify-start min-h-screen bg-blue-900/90 p-6">

    <!-- HEADER -->
    <h1 class="text-4xl font-extrabold text-yellow-400 mb-6">
        Counter Dashboard - {{ $user->counter_id }}
    </h1>

    <!-- SUCCESS MESSAGE -->
    @if(session('success'))
        <div class="bg-green-200 text-green-900 p-4 rounded-lg mb-4 shadow-inner w-full max-w-3xl text-center">
            {{ session('success') }}
        </div>
    @endif

    <div class="w-full max-w-3xl bg-white/10 backdrop-blur-md rounded-3xl shadow-2xl p-6 border border-white/20">

        <!-- TICKETS TABLE -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse rounded-xl overflow-hidden text-gray-100">
                <thead class="bg-blue-800/80 text-left text-yellow-400 uppercase text-sm">
                    <tr>
                        <th class="px-4 py-3">Ticket #</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Created At</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white/10 backdrop-blur-md">
                    @forelse($tickets as $ticket)
                        <tr class="border-b border-white/20 hover:bg-blue-700/50 transition-colors duration-200">
                            <td class="px-4 py-3 font-bold text-lg">{{ $ticket->ticket_number }}</td>
                            <td class="px-4 py-3">
                                @if($ticket->status === 'waiting')
                                    <span class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full text-xs">Waiting</span>
                                @elseif($ticket->status === 'serving')
                                    <span class="bg-green-200 text-green-800 px-2 py-1 rounded-full text-xs">Serving</span>
                                @elseif($ticket->status === 'done')
                                    <span class="bg-gray-200 text-gray-800 px-2 py-1 rounded-full text-xs">Done</span>
                                @else
                                    <span class="bg-gray-300 text-gray-900 px-2 py-1 rounded-full text-xs">{{ ucfirst($ticket->status) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $ticket->created_at->format('H:i:s') }}</td>
                            <td class="px-4 py-3 space-x-2">
                                @if($ticket->status === 'waiting')
                                    <form action="{{ route('counter.serveTicket', $ticket->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-xl shadow transition">
                                            Serve
                                        </button>
                                    </form>
                                @elseif($ticket->status === 'serving')
                                    <form action="{{ route('counter.completeTicket', $ticket->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-xl shadow transition">
                                            Complete
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400 italic">No action</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-gray-300 italic">
                                No tickets assigned yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection
