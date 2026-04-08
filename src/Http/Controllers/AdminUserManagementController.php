<?php

namespace Teguh\FeatureSatuForm\Http\Controllers;

use Teguh\FeatureSatuForm\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserManagementController extends Controller
{
    private const LEVELS = ['guest', 'staff', 'supervisor', 'manager', 'gm', 'director'];

    public function index(): View
    {
        $users = User::query()->orderBy('name')->get();

        return view('feature-satu-form::admin.users.index', [
            'users' => $users,
        ]);
    }

    public function create(): View
    {
        return view('feature-satu-form::admin.users.create', [
            'departments' => ['HR', 'FIN', 'IT', 'OPS'],
            'levels' => self::LEVELS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:FORM.users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:FORM.users,email'],
            'department' => ['required', 'in:HR,FIN,IT,OPS'],
            'level' => ['required', 'in:' . implode(',', self::LEVELS)],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::query()->create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'department' => $validated['department'],
            'level' => $validated['level'],
            'password' => $validated['password'],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user): View
    {
        return view('feature-satu-form::admin.users.edit', [
            'user' => $user,
            'departments' => ['HR', 'FIN', 'IT', 'OPS'],
            'levels' => self::LEVELS,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:FORM.users,username,' . $user->id],
            'email' => ['required', 'email', 'max:255', 'unique:FORM.users,email,' . $user->id],
            'department' => ['required', 'in:HR,FIN,IT,OPS'],
            'level' => ['required', 'in:' . implode(',', self::LEVELS)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $data = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'department' => $validated['department'],
            'level' => $validated['level'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $sessionAdminId = session('admin_id');
        if (!empty($sessionAdminId) && (int) $sessionAdminId === (int) $user->id) {
            return back()->with('error', 'Tidak bisa menghapus user yang sedang login.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}
