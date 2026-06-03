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

            'whatsapp_notification' => [
                'nullable',
                'boolean'
            ],

            'email_notification' => [
                'nullable',
                'boolean'
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
        $user->whatsapp_notification = $request->boolean('whatsapp_notification');
        $user->email_notification = $request->boolean('email_notification');

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

        // Soft-deactivate: hanya ubah status menjadi inactive
        $user->status = 'inactive';
        $user->save();

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')
            ->with(
                'success',
                'Akun berhasil dinonaktifkan. Hubungi admin untuk mengaktifkan kembali.'
            );
    }
}