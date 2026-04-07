@extends('feature-satu-form::public.layout')

@section('title', 'Available Forms')

@section('content')
    <div class="top-links">
        <h1 class="headline">SATU Form</h1>
        <div style="display:flex;gap:10px;align-items:center;">
            <a href="{{ route('public.forms.track') }}">Tracking</a>
            @if(session('public_auth'))
                <span style="font-size:12px;color:#64748b;">{{ session('public_name') }} ({{ strtoupper(session('public_level', 'guest')) }})</span>
                <form method="POST" action="{{ route('public.logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn btn-outline" style="padding:6px 10px;font-size:12px;">Logout</button>
                </form>
            @else
                <a href="{{ route('public.login') }}" class="btn btn-primary" style="padding: 6px 10px; font-size: 12px; color:white">Login</a>
            @endif
            <a href="{{ route('admin.login') }}">Admin Login</a>
        </div>
    </div>

    <p class="sub">Daftar form yang tersedia untuk diisi. Klik "Isi Form" untuk memulai.</p>

    @if($forms->isEmpty())
        <div class="card">
            <p style="text-align: center; color: #64748b;">Tidak ada form yang tersedia saat ini.</p>
        </div>
    @else
        <table id="formsTable">
            <thead>
                <tr>
                    <th style="width: 52px;">No</th>
                    <th>Nama Form</th>
                    <th style="width: 120px;">Dept</th>
                    <th style="width: 90px;">Fields</th>
                    <th style="width: 120px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($forms as $index => $form)
                    @php($dependencyState = $form->dependency_state ?? ['is_blocked' => false, 'message' => null])
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="form-name">{{ $form->name }}</div>
                            <div class="form-desc">{{ $form->description ?? 'No description' }}</div>
                            @if(!empty($form->form_code))
                                <div style="font-size:12px;color:#64748b;margin-top:4px;">Code: {{ $form->form_code }}</div>
                            @endif
                            @if(!empty($form->dependency_form_code))
                                <div style="font-size:12px;color:{{ ($dependencyState['is_blocked'] ?? false) ? '#b91c1c' : '#16a34a' }};margin-top:4px;">
                                    Depends on {{ $form->dependency_form_code }}
                                    @if(($dependencyState['is_blocked'] ?? false) && !empty($dependencyState['message']))
                                        <span> - {{ $dependencyState['message'] }}</span>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td>{{ $form->department ?? '-' }}</td>
                        <td>{{ count($form->fields_config ?? []) }}</td>
                        <td>
                            <a href="{{ route('public.forms.show', $form->id) }}" class="btn {{ !empty($form->dependency_form_code) ? 'btn-outline' : 'btn-primary' }}" style="padding: 6px 10px; font-size: 12px;">
                                {{ !empty($form->dependency_form_code) ? 'Verify Form' : 'Isi Form' }}
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
