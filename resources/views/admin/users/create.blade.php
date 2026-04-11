@extends('feature-satu-form::admin.layout')

@section('title', 'Create User')
@section('active_nav', 'users')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 rounded-3 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 text-primary"><i class="ri-user-add-line me-2"></i>Create User</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted text-uppercase" for="name">Name</label>
                                <input id="name" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted text-uppercase" for="username">Username</label>
                                <input id="username" name="username" value="{{ old('username') }}" class="form-control @error('username') is-invalid @enderror" required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted text-uppercase" for="email">Email</label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted text-uppercase" for="department">Department</label>
                                <select id="department" name="department" class="form-select @error('department') is-invalid @enderror" required>
                                    <option value="">Select...</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department }}" @selected(old('department') === $department)>{{ $department }}</option>
                                    @endforeach
                                </select>
                                @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted text-uppercase" for="level">Level</label>
                                <select id="level" name="level" class="form-select @error('level') is-invalid @enderror" required>
                                    @foreach($levels as $level)
                                        <option value="{{ $level }}" @selected(old('level', 'staff') === $level)>{{ strtoupper($level) }}</option>
                                    @endforeach
                                </select>
                                @error('level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6"></div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted text-uppercase" for="password">Password</label>
                                <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted text-uppercase" for="password_confirmation">Confirm Password</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-1"></i> Save
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                <i class="ri-arrow-left-line me-1"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
