<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Queue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * =========================
     * ADMIN DASHBOARD
     * =========================
     */
    public function index()
    {
        return view('admin.dashboard');
    }

    /**
     * =========================
     * USER MANAGEMENT
     * =========================
     */
    public function users()
    {
        $users = User::latest()->get();
        return view('admin.users', compact('users'));
    }

    public function createUserForm()
    {
        return view('admin.create-user');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'user_id'    => 'required|unique:users,user_id',
            'password'   => [
                'required',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).+$/'
            ],
            'full_name'  => 'required|string|max:255',
            'role'       => 'required|in:admin,counter',
            'counter_id' => 'nullable|integer',
        ]);

        User::create([
            'user_id'    => $request->user_id,
            'password'   => Hash::make($request->password),
            'full_name'  => $request->full_name,
            'role'       => $request->role,
            'counter_id' => $request->role === 'counter' ? $request->counter_id : null,
        ]);

        return redirect()->route('admin.users')
            ->with('success', 'User created successfully.');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit-user', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'user_id'    => 'required|unique:users,user_id,' . $user->id,
            'password'   => [
                'nullable',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).+$/'
            ],
            'full_name'  => 'required|string|max:255',
            'role'       => 'required|in:admin,counter',
            'counter_id' => 'nullable|integer',
        ]);

        $user->user_id    = $request->user_id;
        $user->full_name  = $request->full_name;
        $user->role       = $request->role;
        $user->counter_id = $request->role === 'counter' ? $request->counter_id : null;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users')
            ->with('success', 'User updated successfully.');
    }

    public function deleteUser($id)
    {
        User::findOrFail($id)->delete();

        return redirect()->route('admin.users')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * =========================
     * GENERATE TICKETS (AUTO ASSIGN COUNTER)
     * =========================
     */
    public function addTicket(Request $request)
    {
        $request->validate([
            'ticket_count' => 'required|integer|min:1',
            'counters'     => 'required|array|min:1'
        ]);

        $ticketCount = $request->ticket_count;
        $counters    = $request->counters;

        $lastTicket = Queue::max('ticket_number') ?? 0;

        $counterIndex = 0;
        $counterTotal = count($counters);

        for ($i = 1; $i <= $ticketCount; $i++) {

            $lastTicket++;
            $assignedCounter = $counters[$counterIndex];

            Queue::create([
                'ticket_number' => $lastTicket,
                'counter_id'    => $assignedCounter,
                'status'        => 'waiting',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            $counterIndex++;
            if ($counterIndex >= $counterTotal) {
                $counterIndex = 0;
            }
        }

        return redirect()->back()
            ->with('success', 'Tickets generated and assigned successfully.');
    }

    /**
     * =========================
     * CLEAR ALL TICKETS
     * =========================
     */
    public function clearTickets()
    {
        Queue::truncate();

        return redirect()->back()
            ->with('success', 'All tickets cleared successfully.');
    }

    /**
     * =========================
     * MANUAL ASSIGN TICKETS
     * =========================
     */
    public function assignTicketsToCounter(Request $request)
    {
        $request->validate([
            'counter_id' => 'required|integer',
            'ticket_ids' => 'required|array'
        ]);

        Queue::whereIn('id', $request->ticket_ids)
            ->where('status', 'waiting')
            ->update([
                'counter_id' => $request->counter_id,
                'updated_at' => now()
            ]);

        return redirect()->back()
            ->with('success', 'Tickets assigned successfully.');
    }

    /**
     * =========================
     * DISPLAY SCREEN (TV VIEW)
     * =========================
     */
    public function displayScreen(Request $request)
    {
        $selectedCounters = $request->input('counters', [1,2,3,4,5]);
        $counters = [];

        foreach ($selectedCounters as $i) {

            $ticket = Queue::where('status', 'serving')
                ->where('counter_id', $i)
                ->latest('id')
                ->first();

            $user = User::where('counter_id', $i)->first();

            $counters[$i] = [
                'ticket' => $ticket ? 'C' . str_pad($ticket->ticket_number, 3, '0', STR_PAD_LEFT) : '-',
                'user'   => $user?->full_name ?? 'Unassigned'
            ];
        }

        return view('admin.displayscreen', compact('counters'));
    }

    /**
     * =========================
     * TICKET MANAGEMENT VIEW
     * =========================
     */
    public function ticketManagement()
    {
        $tickets = Queue::orderBy('id', 'asc')->get();
        return view('admin.ticket-management', compact('tickets'));
    }

    public function deleteTicket($id)
    {
        Queue::findOrFail($id)->delete();

        return redirect()->back()
            ->with('success', 'Ticket deleted successfully.');
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

        return redirect()->route('login')
            ->with('success', 'Logged out successfully.');
    }
}
