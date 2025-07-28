<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('agency.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (auth()->guard('agency')->attempt($credentials)) {
            return redirect()->route('agency.dashboard');
        }

        if (auth()->guard('agency')->user()->status == 0) {
            auth()->guard('agency')->logout();
            return back()->with('error', 'Your account is not active');
        }

        return back()->with('error', 'Invalid credentials');
    }

    public function logout()
    {
        auth()->guard('agency')->logout();

        return redirect()->route('agency.login');
    }
}
