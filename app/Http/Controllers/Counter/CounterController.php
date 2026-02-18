<?php

namespace App\Http\Controllers\Counter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CounterController extends Controller
{
    /**
     * =========================
     * Counter Dashboard
     * =========================
     */
    public function index()
    {
        $user = Auth::user();

        // Get full name of logged-in counter
        $fullName = $user->full_name ?? $user->user_id;

        return view('counter.dashboard', [
            'user' => $user,
            'counterId' => $user->counter_id,
            'fullName' => $fullName
        ]);
    }

    /**
     * =========================
     * SERVE NEXT TICKET
     * =========================
     */
    public function serveNextTicket()
    {
        $user = Auth::user();

        if (!$user->counter_id) {
            return response()->json(['message' => 'No counter assigned.'], 400);
        }

        // Check if already serving
        $currentServing = DB::table('queues')
            ->where('counter_id', $user->counter_id)
            ->where('status', 'serving')
            ->first();

        if ($currentServing) {
            return response()->json(['message' => 'Already serving a ticket.'], 400);
        }

        // Get next waiting ticket
        $nextTicket = DB::table('queues')
            ->where('counter_id', $user->counter_id)
            ->where('status', 'waiting')
            ->orderBy('ticket_number')
            ->first();

        if (!$nextTicket) {
            return response()->json(['message' => 'No waiting tickets.'], 400);
        }

        DB::table('queues')
            ->where('id', $nextTicket->id)
            ->update([
                'status' => 'serving',
                'updated_at' => now()
            ]);

        return response()->json(['message' => 'Serving ticket']);
    }

    /**
     * =========================
     * COMPLETE CURRENT TICKET
     * =========================
     */
    public function completeCurrentTicket()
    {
        $user = Auth::user();

        $currentTicket = DB::table('queues')
            ->where('counter_id', $user->counter_id)
            ->where('status', 'serving')
            ->first();

        if (!$currentTicket) {
            return response()->json(['message' => 'No ticket serving.'], 400);
        }

        DB::table('queues')
            ->where('id', $currentTicket->id)
            ->update([
                'status' => 'completed',
                'updated_at' => now()
            ]);

        return response()->json(['message' => 'Ticket completed']);
    }

    /**
     * =========================
     * LIVE STATUS (AJAX)
     * =========================
     */
    public function getStatus()
    {
        $user = Auth::user();

        // Serving ticket number
        $serving = DB::table('queues')
            ->where('counter_id', $user->counter_id)
            ->where('status', 'serving')
            ->value('ticket_number');

        // Waiting tickets count
        $waiting = DB::table('queues')
            ->where('counter_id', $user->counter_id)
            ->where('status', 'waiting')
            ->count();

        // Last completed ticket number
        $lastDone = DB::table('queues')
            ->where('counter_id', $user->counter_id)
            ->where('status', 'completed')
            ->orderByDesc('updated_at')
            ->value('ticket_number');

        // Include full name for frontend display
        $fullName = $user->full_name ?? $user->user_id;

        return response()->json([
            'serving'   => $serving,
            'waiting'   => $waiting,
            'last_done' => $lastDone,
            'full_name' => $fullName
        ]);
    }

    /**
     * =========================
     * LOGOUT
     * =========================
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
