<?php

namespace Teguh\FeatureSatuForm\Http\Controllers;

use Teguh\FeatureSatuForm\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        if ($request->session()->get('admin_auth', false)) {
            return redirect()->route('admin.dashboard');
        }

        return view('feature-satu-form::admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('username', $validated['username'])->first();

        if (!$user || !Hash::check($validated['password'], (string) $user->password)) {
            return back()->withInput()->with('error', 'Username atau password salah.');
        }

        $request->session()->regenerate();
        $request->session()->put('admin_auth', true);
        $request->session()->put('admin_id', $user->id);
        $request->session()->put('admin_name', $user->name);
        $request->session()->put('admin_email', $user->email);
        $request->session()->put('admin_username', $user->username);
        $request->session()->put('admin_department', $user->department);
        $request->session()->put('admin_level', $user->level);

        return redirect()->route('admin.dashboard')->with('success', 'Login berhasil.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(['admin_auth', 'admin_id', 'admin_name', 'admin_email', 'admin_username', 'admin_department', 'admin_level']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Logout berhasil.');
    }
}
