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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
        ]);

        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'name' => $request->input('first_name').' '.$request->input('last_name'),
            'user_name' => $request->input('email'), // Utiliser l'email comme user_name par défaut
        ]);

        $user->assignRole($request->input('role'));

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully!');
    }

    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);

        return Inertia::render('Admin/UserManagement', [
            'user' => $user,
            'users' => User::with('roles')->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|string',
        ]);

        $user->update([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'name' => $request->input('first_name').' '.$request->input('last_name'),
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => $request->input('password')]);
        }

        $user->syncRoles($request->input('role'));

        return redirect()->back()->with('success', 'User updated successfully!');
    }

    public function index()
    {
        $users = User::with('roles')->get();

        return Inertia::render('Admin/UserManagement', [
            'users' => $users,
        ]);
    }
}
