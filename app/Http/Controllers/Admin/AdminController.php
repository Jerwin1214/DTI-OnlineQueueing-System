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
        $users = User::orderBy('id', 'asc')->get();
        return view('admin.users', compact('users'));
    }

    public function createUserForm()
    {
        return view('admin.create-user');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE NEW USER WITH STRONG PASSWORD VALIDATION
    |--------------------------------------------------------------------------
    */
    public function storeUser(Request $request)
    {

        $request->validate([

            'user_id' => [
                'required',
                'string',
                'max:50',
                'unique:users,user_id'
            ],

            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
            ],

            'full_name' => [
                'required',
                'string',
                'max:255'
            ],

            'role' => [
                'required',
                'in:admin,counter'
            ],

            'counter_id' => [
                'nullable',
                'integer',
                'min:1',
                'max:50'
            ]

        ],[
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);


        User::create([
            'user_id' => $request->user_id,
            'password' => Hash::make($request->password),
            'full_name' => $request->full_name,
            'role' => $request->role,
            'counter_id' => $request->role === 'counter'
                ? $request->counter_id
                : null,
            'is_online' => false,
        ]);


        return redirect()->route('admin.users')
            ->with('success', 'User created successfully.');
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT USER
    |--------------------------------------------------------------------------
    */
    public function editUser($id)
    {
        $user = User::findOrFail($id);

        return view('admin.edit-user', compact('user'));
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE USER
    |--------------------------------------------------------------------------
    */
    public function updateUser(Request $request, $id)
    {

        $user = User::findOrFail($id);

        $request->validate([

            'user_id' => [
                'required',
                'string',
                'max:50',
                'unique:users,user_id,' . $user->id
            ],

            'password' => [
                'nullable',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
            ],

            'full_name' => [
                'required',
                'string',
                'max:255'
            ],

            'role' => [
                'required',
                'in:admin,counter'
            ],

            'counter_id' => [
                'nullable',
                'integer',
                'min:1',
                'max:50'
            ]

        ],[
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);


        $user->user_id = $request->user_id;
        $user->full_name = $request->full_name;
        $user->role = $request->role;

        $user->counter_id = $request->role === 'counter'
            ? $request->counter_id
            : null;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();


        return redirect()->route('admin.users')
            ->with('success', 'User updated successfully.');
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE USER
    |--------------------------------------------------------------------------
    */
    public function deleteUser($id)
    {
        User::findOrFail($id)->delete();

        return redirect()->route('admin.users')
            ->with('success', 'User deleted successfully.');
    }


    /*
    |--------------------------------------------------------------------------
    | DISPLAY SCREEN - CURRENT SERVING TICKETS
    |--------------------------------------------------------------------------
    */
    public function getCounters()
    {

        $counters = [1,2,3,4,5];
        $data = [];

        foreach ($counters as $counterId) {

            $ticket = Queue::where('counter_id', $counterId)
                ->where('status', 'serving')
                ->orderBy('id', 'desc')
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
    | COUNTER ONLINE / OFFLINE STATUS
    |--------------------------------------------------------------------------
    */
    public function getCounterStatus()
    {

        $counters = User::where('role', 'counter')
            ->orderBy('counter_id', 'asc')
            ->get();

        $data = [];

        foreach ($counters as $counter) {

            if ($counter->counter_id) {

                $data[$counter->counter_id] = [
                    'user' => $counter->full_name,
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

        $countersParam = $request->query('counters');

        if (is_array($countersParam)) {

            $selectedCounters = $countersParam;

        } elseif (is_string($countersParam)) {

            $selectedCounters = explode(',', $countersParam);

        } else {

            $selectedCounters = [1,2,3,4,5];
        }

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
