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
        $user = Auth::user();
        $counterId = $user->counter_id;

        if (!$counterId) {
            return response()->json(['message' => 'No counter assigned.'], 400);
        }

        // Check if already serving
        $currentServing = DB::table('queues')
            ->where('counter_id', $counterId)
            ->where('status', 'serving')
            ->first();

        if ($currentServing) {
            return response()->json(['message' => 'Already serving a ticket.'], 400);
        }

        // Get next waiting ticket for this counter
        $nextTicket = DB::table('queues')
            ->where('counter_id', $counterId)
            ->where('status', 'waiting')
            ->orderBy('ticket_number', 'asc')
            ->first();

        if (!$nextTicket) {
            return response()->json(['message' => 'No waiting tickets.'], 400);
        }

        // Update to serving
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
        $counterId = $user->counter_id;

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
                'status' => 'done', // 🔥 FIXED (was "completed")
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
        $counterId = $user->counter_id;

        if (!$counterId) {
            return response()->json([
                'serving' => null,
                'waiting' => 0,
                'last_done' => null
            ]);
        }

        // Currently serving ticket
        $serving = DB::table('queues')
            ->where('counter_id', $counterId)
            ->where('status', 'serving')
            ->value('ticket_number');

        // Waiting count
        $waiting = DB::table('queues')
            ->where('counter_id', $counterId)
            ->where('status', 'waiting')
            ->count();

        // Last done ticket
        $lastDone = DB::table('queues')
            ->where('counter_id', $counterId)
            ->where('status', 'done') // 🔥 FIXED
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
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
