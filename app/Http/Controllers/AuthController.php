<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user()->role);
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if ($user->status !== 'aktif') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors(['username' => 'Akun Anda telah dinonaktifkan.'])->withInput();
            }

            $request->session()->regenerate();
            LogAktivitas::catat('Login', 'User ' . $user->nama . ' (' . $user->role . ') berhasil login.');

            return $this->redirectBasedOnRole($user->role);
        }

        return back()->withErrors([
            'username' => 'Username atau password yang Anda masukkan salah.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            LogAktivitas::catat('Logout', 'User ' . Auth::user()->nama . ' telah logout.');
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }

    private function redirectBasedOnRole($role)
    {
        switch ($role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'petugas':
                return redirect()->route('petugas.transaksi.masuk');
            case 'owner':
                return redirect()->route('owner.dashboard');
            default:
                return redirect()->route('login');
        }
    }
}
