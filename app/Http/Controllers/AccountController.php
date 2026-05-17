<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    /**
     * Halaman pengaturan akun
     */
    public function index()
    {
        $user = Auth::user();

        return view('account_settings.index', compact('user'));
    }

    /**
     * Update informasi akun
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'email' => [
                'required',
                'email',
                'unique:users,email,' . $user->id
            ],

            'phone_number' => [
                'nullable',
                'string',
                'max:20'
            ],

            'password' => [
                'nullable',
                'min:8',
                'confirmed'
            ],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone_number = $validated['phone_number'] ?? null;

        // Update password jika diisi
        if ($request->filled('password')) {

            $user->password = Hash::make(
                $validated['password']
            );
        }

        $user->save();

        return redirect()
            ->back()
            ->with(
                'success',
                'Akun berhasil diperbarui!'
            );
    }
    /**
     * Hapus akun
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')
            ->with(
                'success',
                'Akun berhasil dihapus'
            );
    }
}