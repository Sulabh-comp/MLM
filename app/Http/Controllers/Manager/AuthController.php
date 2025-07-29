<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if(auth()->guard('manager')->check()) {
            return redirect()->route('manager.dashboard');
        }

        return view('manager.auth.login');
    }

    public function login(Request $request)
    {
        if(auth()->guard('manager')->check()) {
            return redirect()->route('manager.dashboard');
        }

        $credentials = $request->only('email', 'password');
        // dd($credentials);

        // dd(auth()->guard('manager')->attempt($credentials));

        if (!auth()->guard('manager')->attempt($credentials)) {
            return back()->with('error', 'Invalid credentials');
        }

        if(auth()->guard('manager')->user()->status == 0) {
            auth()->guard('manager')->logout();
            return back()->with('error', 'Your account is not active');
        }
        
        return redirect()->route('manager.dashboard');
    }

    public function logout()
    {
        auth()->guard('manager')->logout();

        return redirect()->route('manager.login');
    }
}
