@extends('feature-satu-form::admin.layout')

@section('title', 'Form Builder')
@section('active_nav', 'forms')

@section('page_styles')
    <style>
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            align-items: end;
        }
        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
@endsection

@section('content')

    <!-- Create Form Card -->
    <div class="card border-0 rounded-3 mb-4">
        <div class="card-header bg-white border-0 rounded-3 py-3">
            <h5 class="mb-0 text-primary"><i class="ri-file-add-line me-2"></i>Create Form</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.forms.store') }}">
                @csrf
                <div class="form-grid">
                    <div>
                        <label class="form-label small fw-semibold text-muted text-uppercase" for="form_code">Form Code</label>
                        <input id="form_code" name="form_code" value="{{ old('form_code') }}" class="form-control" placeholder="FORM-002" required>
                    </div>
                    <div>
                        <label class="form-label small fw-semibold text-muted text-uppercase" for="name">Form Name</label>
                        <input id="name" name="name" value="{{ old('name') }}" class="form-control" required>
                    </div>
                    <div>
                        <label class="form-label small fw-semibold text-muted text-uppercase" for="department">Department</label>
                        <select id="department" name="department" class="form-select" required @if(!empty($userDept)) disabled @endif>
                            <option value="">Select...</option>
                            <option value="HR" @selected((old('department') === 'HR') || (!empty($userDept) && $userDept === 'HR'))>HR</option>
                            <option value="FIN" @selected((old('department') === 'FIN') || (!empty($userDept) && $userDept === 'FIN'))>FIN</option>
                            <option value="IT" @selected((old('department') === 'IT') || (!empty($userDept) && $userDept === 'IT'))>IT</option>
                            <option value="OPS" @selected((old('department') === 'OPS') || (!empty($userDept) && $userDept === 'OPS'))>OPS</option>
                        </select>
                        @if(!empty($userDept))
                            <input type="hidden" name="department" value="{{ $userDept }}">
                        @endif
                    </div>
                    <div>
                        <label class="form-label small fw-semibold text-muted text-uppercase" for="description">Description</label>
                        <input id="description" name="description" value="{{ old('description') }}" class="form-control">
                    </div>
                    <div>
                        <label class="form-label small fw-semibold text-muted text-uppercase" for="dependency_form_code">Dependency Form</label>
                        <select id="dependency_form_code" name="dependency_form_code" class="form-select">
                            <option value="">No dependency</option>
                            @foreach(($dependencyForms ?? []) as $dependencyForm)
                                <option value="{{ $dependencyForm->form_code }}" @selected(old('dependency_form_code') === $dependencyForm->form_code)>
                                    {{ $dependencyForm->form_code }} - {{ $dependencyForm->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ri-add-line me-1"></i> Create
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- All Forms Card -->
    <div class="card border-0 rounded-3">
        <div class="card-header bg-white border-0 rounded-3 py-3">
            <h5 class="mb-0 text-primary"><i class="ri-file-text-line me-2"></i>All Forms</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-uppercase" style="font-size: 11px;">Code</th>
                            <th class="text-uppercase" style="font-size: 11px;">Name</th>
                            <th class="text-uppercase" style="font-size: 11px;">Dept</th>
                            <th class="text-uppercase" style="font-size: 11px;">Dependency</th>
                            <th class="text-uppercase" style="font-size: 11px;">Description</th>
                            <th class="text-uppercase" style="font-size: 11px;">Status</th>
                            <th class="text-uppercase" style="font-size: 11px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($forms as $form)
                            @php($dependencyState = $form->dependency_state ?? ['is_blocked' => false, 'message' => null])
                            <tr>
                                <td><span class="badge bg-secondary mono">{{ $form->form_code ?: '-' }}</span></td>
                                <td>
                                    <div class="fw-semibold">{{ $form->name }}</div>
                                </td>
                                <td><span class="badge bg-light text-dark">{{ $form->department }}</span></td>
                                <td>
                                    @if(!empty($form->dependency_form_code))
                                        <span class="badge bg-warning text-dark">{{ $form->dependency_form_code }}</span>
                                        <div class="small mt-1" style="color: {{ ($dependencyState['is_blocked'] ?? false) ? 'var(--fs-danger)' : 'var(--fs-success)' }};">
                                            {{ ($dependencyState['is_blocked'] ?? false) ? 'Waiting approval' : 'Ready' }}
                                        </div>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $form->description ?: '-' }}</td>
                                <td>
                                    @if($form->is_published)
                                        <span class="badge bg-success">Published</span>
                                    @else
                                        <span class="badge bg-secondary">Draft</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <a href="{{ route('admin.forms.edit', $form) }}" class="btn btn-warning btn-sm text-white">
                                            <i class="ri-edit-line"></i><span class="d-none d-sm-inline ms-1">Edit</span>
                                        </a>
                                        <form method="POST" action="{{ route('admin.forms.togglePublish', $form) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $form->is_published ? 'btn-outline-secondary' : 'btn-success' }}">
                                                {{ $form->is_published ? 'Unpublish' : 'Publish' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.forms.destroy', $form) }}" class="d-inline" onsubmit="return confirm('Delete this form?');">
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
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="ri-file-search-line fs-2 d-block mb-2 opacity-25"></i>
                                    Belum ada form.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($forms->hasPages())
            <div class="card-footer bg-white border-0">
                {{ $forms->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
