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
        $tickets = DB::table('queues')
            ->orderBy('ticket_number', 'asc')
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
        ]);

        DB::beginTransaction();

        try {

            // Get last ticket number safely
            $lastNumber = DB::table('queues')->max('ticket_number');
            $lastNumber = $lastNumber ?? 0;

            for ($i = 1; $i <= $request->ticket_count; $i++) {

                $lastNumber++;

                DB::table('queues')->insert([
                    'ticket_number' => $lastNumber,
                    'counter_id'    => null, // MUST BE NULL
                    'status'        => 'waiting',
                    'created_at'    => now(),
                    'updated_at'    => now()
                ]);
            }

            DB::commit();

            return back()->with('success', 'Tickets added successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', 'Something went wrong while adding tickets.');
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
