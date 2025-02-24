<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function showLoginForm()
    {
        if(auth()->guard('employee')->check()) {
            return redirect()->route('employee.dashboard');
        }

        return view('employee.auth.login');
    }

    public function login(Request $request)
    {
        if(auth()->guard('employee')->check()) {
            return redirect()->route('employee.dashboard');
        }

        $credentials = $request->only('email', 'password');

        if (!auth()->guard('employee')->attempt($credentials)) {

            return back()->with('error', 'Invalid credentials');
        }

        if(auth()->guard('employee')->user()->status == 0) {
            auth()->guard('employee')->logout();
            return back()->with('error', 'Your account is not active');
        }
        return redirect()->route('employee.dashboard');

    }

    public function logout()
    {
        auth()->guard('employee')->logout();

        return redirect()->route('employee.login');
    }
}
