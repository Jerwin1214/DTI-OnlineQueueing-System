<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Queue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ADMIN DASHBOARD
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        return view('admin.dashboard');
    }

    /*
    |--------------------------------------------------------------------------
    | USER MANAGEMENT
    |--------------------------------------------------------------------------
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
            'password'   => 'required|confirmed|min:8',
            'full_name'  => 'required|string|max:255',
            'role'       => 'required|in:admin,counter',
            'counter_id' => 'nullable|integer',
        ]);

        User::create([
            'user_id'    => $request->user_id,
            'password'   => Hash::make($request->password),
            'full_name'  => $request->full_name,
            'role'       => $request->role,
            'counter_id' => $request->role === 'counter'
                                ? $request->counter_id
                                : null,
            'is_online'  => false,
        ]);

        return back()->with('success', 'User created successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | 🔥 DISPLAY SCREEN - CURRENT SERVING TICKETS
    |--------------------------------------------------------------------------
    */
    public function getCounters()
    {
        $counters = [1,2,3,4,5];
        $data = [];

        foreach ($counters as $counterId) {

            $ticket = Queue::where('counter_id', $counterId)
                ->where('status', 'serving')
                ->latest('updated_at')
                ->first();

            $data[$counterId] = [
                'ticket' => $ticket
                    ? 'C' . str_pad($ticket->ticket_number, 3, '0', STR_PAD_LEFT)
                    : null
            ];
        }

        return response()->json($data);
    }

    /*
    |--------------------------------------------------------------------------
    | 🔥 COUNTER ONLINE / OFFLINE STATUS
    |--------------------------------------------------------------------------
    */
    public function getCounterStatus()
    {
        $counters = User::where('role', 'counter')->get();
        $data = [];

        foreach ($counters as $counter) {

            if ($counter->counter_id) {

                $data[$counter->counter_id] = [
                    'user'   => $counter->full_name,
                    'status' => $counter->is_online ? 'online' : 'offline'
                ];
            }
        }

        return response()->json($data);
    }

    /*
    |--------------------------------------------------------------------------
    | DISPLAY SCREEN VIEW
    |--------------------------------------------------------------------------
    */
    public function displayScreen(Request $request)
    {
        $selectedCounters = $request->input('counters', [1,2,3,4,5]);

        return view('admin.displayscreen', compact('selectedCounters'));
    }

    /*
    |--------------------------------------------------------------------------
    | TICKET MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function ticketManagement()
    {
        $tickets = Queue::orderBy('id', 'asc')->get();
        return view('admin.ticket-management', compact('tickets'));
    }

    public function deleteTicket($id)
    {
        Queue::findOrFail($id)->delete();

        return back()->with('success', 'Ticket deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | CLEAR ALL TICKETS
    |--------------------------------------------------------------------------
    */
    public function clearTickets()
    {
        Queue::truncate();

        return back()->with('success', 'All tickets cleared successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
