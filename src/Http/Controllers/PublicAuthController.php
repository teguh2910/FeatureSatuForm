<?php

namespace Teguh\FeatureSatuForm\Http\Controllers;

use Teguh\FeatureSatuForm\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PublicAuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        if ($request->session()->get('public_auth', false)) {
            return redirect()->route('public.forms.index');
        }

        return view('feature-satu-form::public.login', [
            'redirectTo' => (string) $request->query('redirect_to', route('public.forms.index')),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'redirect_to' => ['nullable', 'string'],
        ]);

        $user = User::query()->where('username', $validated['username'])->first();

        if (!$user || !Hash::check($validated['password'], (string) $user->password)) {
            return back()->withInput()->with('error', 'Username atau password salah.');
        }

        $request->session()->regenerate();
        $request->session()->put('public_auth', true);
        $request->session()->put('public_user_id', $user->id);
        $request->session()->put('public_name', $user->name);
        $request->session()->put('public_email', $user->email);
        $request->session()->put('public_username', $user->username);
        $request->session()->put('public_department', $user->department);
        $request->session()->put('public_level', $user->level);

        $redirectTo = (string) ($validated['redirect_to'] ?? route('public.forms.index'));
        if (!str_starts_with($redirectTo, '/') && !filter_var($redirectTo, FILTER_VALIDATE_URL)) {
            $redirectTo = route('public.forms.index');
        }

        return redirect($redirectTo)->with('success', 'Login berhasil.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(['public_auth', 'public_user_id', 'public_name', 'public_email', 'public_username', 'public_department', 'public_level']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('public.forms.index')->with('success', 'Logout berhasil.');
    }
}
