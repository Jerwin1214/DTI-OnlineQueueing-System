<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    /**
     * ====================
     * Display Tickets
     * ====================
     */
    public function index()
    {
        // 🔥 FIX: Always sort by ID (real queue order)
        $tickets = DB::table('queues')
            ->orderBy('id', 'asc')
            ->get();

        return view('admin.ticket-management', compact('tickets'));
    }

    /**
     * ====================
     * Add Tickets
     * ====================
     */
    public function add(Request $request)
    {
        $request->validate([
            'ticket_count' => 'required|integer|min:1|max:100',
            'counters'     => 'nullable|array'
        ]);

        DB::beginTransaction();

        try {

            // Get last ticket number safely
            $lastNumber = DB::table('queues')->max('ticket_number') ?? 0;

            $counters = $request->has('counters') ? $request->counters : [];

            for ($i = 1; $i <= $request->ticket_count; $i++) {

                $lastNumber++;

                if (!empty($counters)) {
                    $counterIndex = ($i - 1) % count($counters);
                    $assignedCounter = (int) $counters[$counterIndex];
                } else {
                    $assignedCounter = null;
                }

                DB::table('queues')->insert([
                    'ticket_number' => $lastNumber,
                    'counter_id'    => $assignedCounter,
                    'status'        => 'waiting',
                    'created_at'    => now(),
                    'updated_at'    => now()
                ]);
            }

            DB::commit();

            return back()->with('success', 'Tickets added successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', 'Error adding tickets.');
        }
    }

    /**
     * ====================
     * Delete Ticket
     * ====================
     */
    public function delete($id)
    {
        DB::table('queues')
            ->where('id', $id)
            ->delete();

        return back()->with('success', 'Ticket deleted successfully.');
    }

    /**
     * ====================
     * Clear All Tickets
     * ====================
     */
    public function clear()
    {
        DB::table('queues')->truncate();

        return back()->with('success', 'All tickets cleared successfully.');
    }
}
