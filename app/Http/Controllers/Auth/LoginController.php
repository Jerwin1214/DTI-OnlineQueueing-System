<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // Blade file we'll create next
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = [
            'user_id' => $request->username, // matches LRN / username column
            'password' => $request->password
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->counter_id > 0) {
                return redirect()->route('counter.dashboard', ['id' => $user->counter_id]);
            } else {
                return back()->withErrors(['username' => 'Role not assigned correctly.']);
            }
        }

        return back()->withErrors(['username' => 'Invalid username or password.']);
    }
}
