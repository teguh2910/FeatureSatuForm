@extends('feature-satu-form::admin.layout')

@section('title', 'User Management')
@section('active_nav', 'users')

@section('content')
    <div class="card border-0 rounded-3">
        <div class="card-header bg-white border-0 rounded-3 d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 text-primary"><i class="ri-user-settings-line me-2"></i>Users</h5>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                <i class="ri-user-add-line me-1"></i> Add User
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-uppercase" style="font-size: 11px;">Name</th>
                            <th class="text-uppercase" style="font-size: 11px;">Username</th>
                            <th class="text-uppercase" style="font-size: 11px;">Email</th>
                            <th class="text-uppercase" style="font-size: 11px;">Department</th>
                            <th class="text-uppercase" style="font-size: 11px;">Level</th>
                            <th class="text-uppercase" style="font-size: 11px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="fw-semibold">{{ $user->name }}</td>
                                <td><span class="badge bg-secondary">{{ $user->username }}</span></td>
                                <td class="small text-muted">{{ $user->email }}</td>
                                <td><span class="badge bg-light text-dark">{{ $user->department }}</span></td>
                                <td>
                                    @php($levelColors = ['admin' => 'bg-danger', 'super_admin' => 'bg-danger', 'manager' => 'bg-warning text-dark', 'supervisor' => 'bg-info', 'staff' => 'bg-secondary'])
                                    <span class="badge {{ $levelColors[strtolower($user->level ?? 'staff')] ?? 'bg-secondary' }}">
                                        {{ strtoupper($user->level ?? 'staff') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm text-white">
                                            <i class="ri-edit-line"></i><span class="d-none d-sm-inline ms-1">Edit</span>
                                        </a>
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('Delete user ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="ri-delete-bin-line"></i><span class="d-none d-sm-inline ms-1">Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="ri-user-search-line fs-2 d-block mb-2 opacity-25"></i>
                                    Belum ada user.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
