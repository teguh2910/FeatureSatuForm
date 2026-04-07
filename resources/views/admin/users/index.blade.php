@extends('feature-satu-form::admin.layout')

@section('title', 'User Management')

@section('active_nav', 'users')

@section('page_styles')
    .actions { display: flex; gap: 6px; flex-wrap: wrap; }
@endsection

@section('content')
    <section class="card" style="overflow:auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
            <h3 style="margin:0;color:var(--primary);">Users</h3>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add User</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Level</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->department }}</td>
                        <td>{{ strtoupper($user->level ?? 'staff') }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">Edit</a>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete user ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;color:#64748b;">Belum ada user.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
