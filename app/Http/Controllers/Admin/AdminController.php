<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Queue;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Admin Dashboard (MAIN PANEL)
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

    /**
     * List all users
     */
    public function users()
    {
        $users = User::latest()->get();
        return view('admin.users', compact('users'));
    }

    /**
     * Show form to create a new user
     */
    public function createUserForm()
    {
        return view('admin.create-user');
    }

    /**
     * Store newly created user
     */
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
        ], [
            'password.regex' => 'Password must contain uppercase, lowercase, number, and special character.',
        ]);

        User::create([
            'user_id'    => $request->user_id,
            'password'   => Hash::make($request->password),
            'full_name'  => $request->full_name,
            'role'       => $request->role,
            'counter_id' => $request->counter_id ?? null,
        ]);

        return redirect()->route('admin.users')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show form to edit existing user
     */
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit-user', compact('user'));
    }

    /**
     * Update existing user
     */
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
        ], [
            'password.regex' => 'Password must contain uppercase, lowercase, number, and special character.',
        ]);

        $user->user_id    = $request->user_id;
        $user->full_name  = $request->full_name;
        $user->role       = $request->role;
        $user->counter_id = $request->counter_id ?? null;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Delete a user
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * =========================
     * DISPLAY SCREEN (TV VIEW)
     * =========================
     */
    public function displayScreen(Request $request)
    {
        $selectedCounters = $request->input('counters', [1, 2, 3, 4, 5]);
        $counters = [];

        foreach ($selectedCounters as $i) {
            $ticket = Queue::where('status', 'serving')
                ->where('counter_id', $i)
                ->latest('id')
                ->first();

            $user = User::where('counter_id', $i)->first();
            $counters[$i] = [
                'ticket' => $ticket?->ticket_number ?? '-',
                'user'   => $user?->full_name ?? 'Unassigned'
            ];
        }

        return view('admin.displayscreen', compact('counters'));
    }

    /**
     * AJAX polling endpoint for tickets
     */
    public function getCounters()
    {
        $counters = [];

        for ($i = 1; $i <= 5; $i++) {
            $ticket = Queue::where('status', 'serving')
                ->where('counter_id', $i)
                ->latest('id')
                ->first();

            $user = User::where('counter_id', $i)->first();

            $counters[$i] = [
                'ticket' => $ticket?->ticket_number ?? '-',
                'user'   => $user?->full_name ?? 'Unassigned'
            ];
        }

        return response()->json($counters);
    }

    /**
     * AJAX endpoint for online/offline counter status
     */
    public function getCounterStatus()
    {
        $counters = [];

        for ($i = 1; $i <= 5; $i++) {
            $user = User::where('counter_id', $i)->first();

            if ($user) {
                // Check if user has an active session in last 5 min
                $isOnline = DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->where('last_activity', '>=', now()->subMinutes(5)->timestamp)
                    ->exists();

                $counters[$i] = [
                    'user' => $user->full_name ?? $user->user_id,
                    'status' => $isOnline ? 'online' : 'offline'
                ];
            }
        }

        return response()->json($counters);
    }

    /**
     * =========================
     * TICKET MANAGEMENT
     * =========================
     */
    public function ticketManagement()
    {
        $tickets = Ticket::latest()->get();
        return view('admin.ticket-management', compact('tickets'));
    }

    public function deleteTicket($id)
    {
        Ticket::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Ticket deleted successfully.');
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
