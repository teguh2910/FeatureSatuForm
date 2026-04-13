@extends('feature-satu-form::admin.layout')

@section('title', 'Form Builder')

@section('active_nav', 'forms')

@section('page_styles')
    .grid { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; gap: 10px; align-items: end; }
    .badge { display: inline-flex; padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 700; }
    .published { background: #dcfce7; color: #16a34a; }
    .draft { background: #e2e8f0; color: #334155; }
    .actions { display: flex; gap: 6px; flex-wrap: wrap; }
    @media (max-width: 980px) { .grid { grid-template-columns: 1fr; } }
@endsection

@section('content')
    <section class="card">
        <h3 style="margin:0 0 12px;color:var(--primary);">Create Form</h3>
        <form method="POST" action="{{ route('admin.forms.store') }}">
            @csrf
            <div class="grid">
                <div>
                    <label for="form_code">Form Code</label>
                    <input id="form_code" name="form_code" value="{{ old('form_code') }}" placeholder="FORM-002" required>
                </div>
                <div>
                    <label for="name">Form Name</label>
                    <input id="name" name="name" value="{{ old('name') }}" required>
                </div>
                <div>
                    <label for="department">Department</label>
                    <select id="department" name="department" required @if(!empty($userDept)) disabled @endif>
                        <option value="">Select</option>
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
                    <label for="description">Description</label>
                    <input id="description" name="description" value="{{ old('description') }}">
                </div>
                <div>
                    <label for="dependency_form_code">Dependency Form</label>
                    <select id="dependency_form_code" name="dependency_form_code">
                        <option value="">No dependency</option>
                        @foreach(($dependencyForms ?? []) as $dependencyForm)
                            <option value="{{ $dependencyForm->form_code }}" @selected(old('dependency_form_code') === $dependencyForm->form_code)>
                                {{ $dependencyForm->form_code }} - {{ $dependencyForm->name }} (Published)
                            </option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-primary" type="submit">Create</button>
            </div>
        </form>
    </section>

    <section class="card" style="overflow:auto;">
        <h3 style="margin:0 0 12px;color:var(--primary);">All Forms</h3>
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Dependency</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($forms as $form)
                    <tr>
                        <td>{{ $form->form_code ?: '-' }}</td>
                        <td><strong>{{ $form->name }}</strong></td>
                        <td>{{ $form->department }}</td>
                        <td>
                            @if(!empty($form->dependency_form_code))
                                <div>{{ $form->dependency_form_code }}</div>
                                @php($dependencyState = $form->dependency_state ?? ['is_blocked' => false, 'message' => null])
                                <div style="font-size:12px;color:{{ ($dependencyState['is_blocked'] ?? false) ? '#b91c1c' : '#16a34a' }};">
                                    {{ ($dependencyState['is_blocked'] ?? false) ? 'Waiting approval' : 'Ready' }}
                                </div>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $form->description ?: '-' }}</td>
                        <td>
                            @if($form->is_published)
                                <span class="badge published">Published</span>
                            @else
                                <span class="badge draft">Draft</span>
                            @endif
                        </td>
                        <td>
                            <div class="actions">
                                <form method="GET" action="{{ route('admin.forms.edit', $form) }}">
                                    @csrf
                                    <button class="btn btn-warning" type="submit">Edit</button>
                                </form>
                                <form method="POST" action="{{ route('admin.forms.togglePublish', $form) }}">
                                    @csrf
                                    <button class="btn btn-success" type="submit">{{ $form->is_published ? 'Unpublish' : 'Publish' }}</button>
                                </form>
                                <form method="POST" action="{{ route('admin.forms.destroy', $form) }}" onsubmit="return confirm('Delete this form?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;color:#64748b;">Belum ada form.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top:12px;">{{ $forms->links() }}</div>
    </section>
@endsection
