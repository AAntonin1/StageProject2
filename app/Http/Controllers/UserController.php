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

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users,user_name',
            'password' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'role' => 'required|string',
        ]);

        $user = User::create([
            'user_name' => $request->input('username'),
            'password' => $request->input('password'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'name' => $request->input('first_name').' '.$request->input('last_name'),
        ]);

        // Put role user
        $role = $request->input('role');
        $user->assignRole($role);


        return redirect()->back()->with('success', 'Expense report added successfully!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully!');
    }
}
