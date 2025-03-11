<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('pages.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('username', 'password'))) {
            return redirect()->back()->withError('Username or password incorrect');
        }

        return redirect()->intended(route('home'))->withSuccess('Login successfully');
    }


    public function logout()
    {
        Auth::logout();
        \request()->session()->regenerateToken();
        \request()->session()->invalidate();

        return redirect(route('login'))->withSuccess('logout successfully');
    }
}
