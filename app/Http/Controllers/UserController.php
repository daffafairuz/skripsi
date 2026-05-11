<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $users = User::latest()->get();

        return view('users.index', compact('users'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('users.create');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([

            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'nullable',
            'role' => 'required|in:admin,user',
            'password' => 'required|min:6',

        ]);

        User::create([

            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'role' => $request->role,
            'status' => 'active',
            'password' => Hash::make($request->password),

        ]);

        return redirect('/users')
            ->with('success', 'User berhasil ditambahkan');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, User $user)
    {
        $request->validate([

            'name' => 'required',
            'email' => 'required|email',
            'phone_number' => 'nullable',
            'role' => 'required|in:admin,user',
            'status' => 'required|in:active,inactive',

        ]);

        $user->update([

            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'role' => $request->role,
            'status' => $request->status,

        ]);

        return redirect('/users')
            ->with('success', 'User berhasil diperbarui');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(User $user)
    {
        $user->delete();

        return redirect('/users')
            ->with('success', 'User berhasil dihapus');
    }
}