<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'badge_id'   => 'required|string',
            'kata_sandi' => 'required|string',
        ]);

        $user = \App\Models\User::where(
            'badge_id',
            $credentials['badge_id']
        )->first();

        if (! $user) {
            return back()
                ->withErrors([
                    'badge_id' => 'Badge ID atau password salah.',
                ])
                ->onlyInput('badge_id');
        }

        if (! $user->is_active) {
            return back()
                ->withErrors([
                    'badge_id' => 'Akun Anda telah dinonaktifkan. Silakan hubungi HRD untuk informasi lebih lanjut.',
                ])
                ->onlyInput('badge_id');
        }

        if (! Auth::attempt([
            'badge_id'  => $credentials['badge_id'],
            'password'  => $credentials['kata_sandi'],
        ])) {
            return back()
                ->withErrors([
                    'badge_id' => 'Badge ID atau password salah.',
                ])
                ->onlyInput('badge_id');
        }

        $request->session()->regenerate();

        return $this->redirectByRole(
            Auth::user()->role
        );
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectByRole(string $role): RedirectResponse
    {
        return match ($role) {
            'HRD'      => redirect()->route('hrd.dashboard.index'),
            'MIS'      => redirect()->route('mis.dashboard.index'),
            'HOD'      => redirect()->route('hod.dashboard.index'),
            default    => redirect()->route('karyawan.dashboard'),
        };
    }
}