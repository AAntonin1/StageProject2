<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            'user_name' => 'required|string|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
        ]);

        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'user_name' => $request->input('user_name'),
            'password' => Hash::make($request->input('password')),
            'name' => $request->input('first_name').' '.$request->input('last_name'),
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

        $rules = [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users,email,'.$id,
            'user_name' => 'required|string|unique:users,user_name,'.$id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|string',
        ];

        $request->validate($rules);

        $updateData = [
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'user_name' => $request->input('user_name'),
            'name' => $request->input('first_name').' '.$request->input('last_name'),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->input('password'));
        }

        $user->update($updateData);
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

    public function profile() {
        $user = auth()->user();
        $expenseReport = $user->expense_report()->get();

        return Inertia::render('Users/Profile', [
            'user' => $user,
            'expenseReport' => $expenseReport[0],
        ]);
    }

    public function updateProfile (Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'job' => 'required|string',
            'vehicle' => 'required|string',
            'number_plate' => 'required|string',
            'km_rate' => 'required|numeric',
            'address_home' => 'required',
            'address_work' => 'required',
            'home_work_distance' => 'required|numeric',
        ]);

        $user = auth()->user();

        // Update user table
        $userUpdateData = [
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'address_home' => $request->input('address_home'),
            'address_work' => $request->input('address_work'),
            'home_work_distance' => $request->input('home_work_distance'),
        ];

        $user->update($userUpdateData);

        // Update expense_report table
        $expenseReport = $user->expense_report()->latest()->first();
        if ($expenseReport) {
            $expenseReport->update([
                'number_plate' => $request->input('number_plate'),
                'km_rate' => $request->input('km_rate'),
                'job' => $request->input('job'),
                'vehicle' => $request->input('vehicle'),
            ]);
        }

        return redirect()->route('user.profile')->with('success', 'Profile updated successfully!');
    }
}
