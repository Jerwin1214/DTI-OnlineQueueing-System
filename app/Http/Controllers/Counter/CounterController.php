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

        if (!$user || !$user->counter_id) {
            abort(403, 'No counter assigned.');
        }

        // Mark counter user as online
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'is_online' => true,
                'updated_at' => now()
            ]);

        return view('counter.dashboard', [
            'user'      => $user,
            'counterId' => $user->counter_id,
            'fullName'  => $user->full_name ?? $user->user_id
        ]);
    }

    /**
     * =========================
     * SERVE NEXT TICKET (FIXED)
     * =========================
     */
    public function serveNextTicket()
    {
        $counterId = Auth::user()->counter_id;

        if (!$counterId) {
            return response()->json([
                'message' => 'No counter assigned.'
            ], 400);
        }

        // Prevent double serving
        $currentServing = DB::table('queues')
            ->where('counter_id', $counterId)
            ->where('status', 'serving')
            ->first();

        if ($currentServing) {
            return response()->json([
                'message' => 'Already serving a ticket.',
                'ticket'  => $currentServing->ticket_number
            ], 400);
        }

        // Get oldest waiting ticket assigned to this counter
        $nextTicket = DB::table('queues')
            ->where('counter_id', $counterId)
            ->where('status', 'waiting')
            ->orderBy('id', 'asc')
            ->first();

        if (!$nextTicket) {
            return response()->json([
                'message' => 'No waiting tickets for this counter.'
            ], 400);
        }

        // Update to serving
        DB::table('queues')
            ->where('id', $nextTicket->id)
            ->update([
                'status'     => 'serving',
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
            return response()->json([
                'message' => 'No counter assigned.'
            ], 400);
        }

        $currentTicket = DB::table('queues')
            ->where('counter_id', $counterId)
            ->where('status', 'serving')
            ->first();

        if (!$currentTicket) {
            return response()->json([
                'message' => 'No ticket currently serving.'
            ], 400);
        }

        DB::table('queues')
            ->where('id', $currentTicket->id)
            ->update([
                'status'     => 'done',
                'updated_at' => now()
            ]);

        return response()->json([
            'message' => 'Ticket completed',
            'ticket'  => $currentTicket->ticket_number
        ]);
    }

    /**
     * =========================
     * LIVE STATUS (FIXED)
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

        // WAITING tickets assigned to this counter
        $waiting = DB::table('queues')
            ->where('counter_id', $counterId)
            ->where('status', 'waiting')
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

        if ($user) {
            DB::table('users')
                ->where('id', $user->id)
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
