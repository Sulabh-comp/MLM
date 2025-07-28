<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('manager.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (auth()->guard('manager')->attempt($credentials)) {
            return redirect()->route('manager.dashboard');
        }

        return back()->with('error', 'Invalid credentials');
    }

    public function logout()
    {
        auth()->guard('manager')->logout();

        return redirect()->route('manager.login');
    }
}
