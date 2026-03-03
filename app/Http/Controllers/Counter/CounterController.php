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
     * SERVE NEXT TICKET
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

        $currentServing = DB::table('queues')
            ->where('counter_id', $counterId)
            ->where('status', 'serving')
            ->first();

        if ($currentServing) {
            return response()->json([
                'message' => 'Already serving a ticket.',
                'ticket'  => $this->formatTicket($currentServing->ticket_number)
            ], 400);
        }

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

        DB::table('queues')
            ->where('id', $nextTicket->id)
            ->update([
                'status'     => 'serving',
                'updated_at' => now()
            ]);

        return response()->json([
            'message' => 'Serving ticket',
            'ticket'  => $this->formatTicket($nextTicket->ticket_number)
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
            'ticket'  => $this->formatTicket($currentTicket->ticket_number)
        ]);
    }

    /**
     * =========================
     * LIVE STATUS (NOW FORMATTED)
     * =========================
     */
    public function getStatus()
    {
        $counterId = Auth::user()->counter_id;

        $servingRaw = DB::table('queues')
            ->where('counter_id', $counterId)
            ->where('status', 'serving')
            ->value('ticket_number');

        $serving = $servingRaw
            ? $this->formatTicket($servingRaw)
            : null;

        $waiting = DB::table('queues')
            ->where('counter_id', $counterId)
            ->where('status', 'waiting')
            ->count();

        $done = DB::table('queues')
            ->where('counter_id', $counterId)
            ->where('status', 'done')
            ->count();

        return response()->json([
            'serving'   => $serving,   // 🔥 NOW C000 FORMAT
            'waiting'   => $waiting,
            'last_done' => $done
        ]);
    }

    /**
     * =========================
     * FORMAT TO C000
     * =========================
     */
    private function formatTicket($number)
    {
        return 'C' . str_pad($number, 3, '0', STR_PAD_LEFT);
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
