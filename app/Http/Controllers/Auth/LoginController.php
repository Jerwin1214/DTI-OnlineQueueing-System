<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = [
            'user_id'  => $request->username,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();

            $user = Auth::user();

            // =========================
            // SET COUNTER ONLINE
            // =========================
            if ($user->role === 'counter') {

                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'is_online' => true
                    ]);

                return redirect()->route('counter.dashboard');
            }

            // =========================
            // ADMIN LOGIN
            // =========================
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            // If role invalid
            return back()->withErrors([
                'username' => 'Role not assigned correctly.'
            ]);
        }

        return back()->withErrors([
            'username' => 'Invalid username or password.'
        ]);
    }
}
