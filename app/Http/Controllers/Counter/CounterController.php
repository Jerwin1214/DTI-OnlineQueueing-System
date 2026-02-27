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

        if (!$user->counter_id) {
            abort(403, 'No counter assigned.');
        }

        // ✅ MARK COUNTER AS ONLINE
        DB::table('counters')
            ->where('id', $user->counter_id)
            ->update([
                'is_online' => true,
                'updated_at' => now()
            ]);

        return view('counter.dashboard', [
            'user' => $user,
            'counterId' => $user->counter_id,
            'fullName' => $user->full_name ?? $user->user_id
        ]);
    }

    /**
     * =========================
     * SERVE NEXT TICKET
     * =========================
     */
    public function serveNextTicket()
    {
        $counterId = Auth::user()->counter_id;

        if (!$counterId) {
            return response()->json(['message' => 'No counter assigned.'], 400);
        }

        // Check if already serving
        $currentServing = DB::table('queues')
            ->where('counter_id', $counterId)
            ->where('status', 'serving')
            ->first();

        if ($currentServing) {
            return response()->json([
                'message' => 'Already serving a ticket.',
                'ticket' => $currentServing->ticket_number
            ], 400);
        }

        // ✅ GET OLDEST UNASSIGNED WAITING TICKET
        $nextTicket = DB::table('queues')
            ->where('status', 'waiting')
            ->whereNull('counter_id')
            ->orderBy('id', 'asc')
            ->first();

        if (!$nextTicket) {
            return response()->json(['message' => 'No waiting tickets.'], 400);
        }

        // ✅ ASSIGN TICKET TO THIS COUNTER
        DB::table('queues')
            ->where('id', $nextTicket->id)
            ->update([
                'status' => 'serving',
                'counter_id' => $counterId,
                'updated_at' => now()
            ]);

        return response()->json([
            'message' => 'Serving ticket',
            'ticket'  => $nextTicket->ticket_number
        ]);
    }

    /**
     * =========================
     * COMPLETE CURRENT TICKET
     * =========================
     */
    public function completeCurrentTicket()
    {
        $counterId = Auth::user()->counter_id;

        if (!$counterId) {
            return response()->json(['message' => 'No counter assigned.'], 400);
        }

        $currentTicket = DB::table('queues')
            ->where('counter_id', $counterId)
            ->where('status', 'serving')
            ->first();

        if (!$currentTicket) {
            return response()->json(['message' => 'No ticket serving.'], 400);
        }

        DB::table('queues')
            ->where('id', $currentTicket->id)
            ->update([
                'status' => 'done',
                'updated_at' => now()
            ]);

        return response()->json([
            'message' => 'Ticket completed',
            'ticket'  => $currentTicket->ticket_number
        ]);
    }

    /**
     * =========================
     * LIVE STATUS (AJAX)
     * =========================
     */
    public function getStatus()
    {
        $counterId = Auth::user()->counter_id;

        if (!$counterId) {
            return response()->json([
                'serving'   => null,
                'waiting'   => 0,
                'last_done' => null
            ]);
        }

        // Currently serving
        $serving = DB::table('queues')
            ->where('counter_id', $counterId)
            ->where('status', 'serving')
            ->value('ticket_number');

        // Count all UNASSIGNED waiting tickets
        $waiting = DB::table('queues')
            ->where('status', 'waiting')
            ->whereNull('counter_id')
            ->count();

        // Last completed ticket
        $lastDone = DB::table('queues')
            ->where('counter_id', $counterId)
            ->where('status', 'done')
            ->orderByDesc('updated_at')
            ->value('ticket_number');

        return response()->json([
            'serving'   => $serving,
            'waiting'   => $waiting,
            'last_done' => $lastDone
        ]);
    }

    /**
     * =========================
     * LOGOUT
     * =========================
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        // ✅ MARK COUNTER OFFLINE
        if ($user && $user->counter_id) {
            DB::table('counters')
                ->where('id', $user->counter_id)
                ->update([
                    'is_online' => false,
                    'updated_at' => now()
                ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
