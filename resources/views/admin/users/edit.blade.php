@extends('feature-satu-form::admin.layout')

@section('title', 'Edit User')

@section('active_nav', 'users')

@section('page_styles')
    .wrap { max-width: 900px; }
    .row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .form-group { margin-bottom: 12px; }
    .error { color: var(--danger); font-size: 12px; margin-top: 4px; }
    .actions { margin-top: 14px; display:flex; gap:8px; }
@endsection

@section('content')
    <section class="card">
        <h3 style="margin-top:0;color:var(--primary);">Edit User</h3>
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input id="username" name="username" value="{{ old('username', $user->username) }}" required>
                    @error('username')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="department">Department</label>
                    <select id="department" name="department" required>
                        <option value="">Select</option>
                        @foreach($departments as $department)
                            <option value="{{ $department }}" @selected(old('department', $user->department) === $department)>{{ $department }}</option>
                        @endforeach
                    </select>
                    @error('department')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label for="level">Level</label>
                    <select id="level" name="level" required>
                        @foreach($levels as $level)
                            <option value="{{ $level }}" @selected(old('level', $user->level) === $level)>{{ strtoupper($level) }}</option>
                        @endforeach
                    </select>
                    @error('level')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div></div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label for="password">New Password (optional)</label>
                    <input id="password" type="password" name="password">
                    @error('password')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation">
                </div>
            </div>
            <div class="actions">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </section>
@endsection
