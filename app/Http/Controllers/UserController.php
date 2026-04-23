<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserController extends Controller
{
    public function login()
    {
        return Inertia::render('Users/Login', [
        ]);
    }

    public function checkLogin(Request $request)
    {
        $credentials = $request->only('password', 'user_name');

        if (Auth::attempt($credentials)) {
            $user = User::where('user_name', $request->input('user_name'))->first();
            $user->assignRole('user');
            Auth::login($user);

            return redirect()->route('expenseReport.form')->with('success', 'Expense report created successfully!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login');
    }
}
