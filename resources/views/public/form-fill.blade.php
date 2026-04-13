@extends('feature-satu-form::public.layout')

@section('title', $form->name)

@section('content')
    <div class="top-links">
        <a href="{{ route('public.forms.index') }}" style="text-decoration: none;">← Back</a>
        <h1 class="headline">{{ $form->name }}</h1>
    </div>

    <p class="sub">{{ $form->description ?? '' }}</p>

    @php
        $hasDependency = !empty($dependencyState['has_dependency'] ?? false);
        $dependencyForm = $dependencyState['dependency_form'] ?? null;
        $dependencyVerified = !empty($dependencyVerified ?? false);
        $dependencyVerificationTracking = (string) ($dependencyVerification['tracking_id'] ?? old('dependency_tracking_id', ''));
    @endphp

    @if($hasDependency && !$dependencyVerified)
        <div class="card" style="border-color:#f59e0b;background:#fffbeb;color:#92400e;margin-bottom:16px;">
            <strong>Dependency Required</strong>
            <div style="margin-top:6px;">
                Form ini bergantung pada {{ $dependencyState['dependency_form']->form_code ?? 'dependency' }}.
                Masukkan nomor track dari form dependency yang sudah approved, lalu klik Verify.
            </div>
            @if(!empty($dependencyState['message']))
                <div style="margin-top:6px;">{{ $dependencyState['message'] }}</div>
            @endif
        </div>

        @if (session('success'))
            <div class="card" style="border-color:#dcfce7;background:#f0fdf4;color:#166534;">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="card" style="border-color:#fecaca;background:#fff1f2;color:#b91c1c;">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('public.forms.verify-dependency', $form->id) }}" style="margin-bottom:16px;">
            @csrf
            <div class="form-group">
                <label for="dependency_tracking_id" style='color:black'>Dependency Track Number</label>
                <input id="dependency_tracking_id" name="dependency_tracking_id" value="{{ old('dependency_tracking_id', $dependencyVerificationTracking) }}" placeholder="TRK-XXXXXXXXXX" required>
                @error('dependency_tracking_id')<div style="color:#b91c1c;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
            <div class="actions" style="margin-top:12px;">
                <a href="{{ route('public.forms.index') }}" class="btn btn-outline">Back</a>
                <button type="submit" class="btn btn-primary">Verify</button>
            </div>
        </form>
    @else
    <div class="card" style="margin-bottom:16px; background:#f0fdf4; border:1px solid #bbf7d0; color:#166534;">
        <strong>Dependency Verified</strong>
        <div style="margin-top:6px;">{{ $dependencyVerification['tracking_id'] ?? 'Dependency sudah diverifikasi.' }}</div>
    </div>

    <form method="POST" action="{{ route('public.forms.store', $form->id) }}" enctype="multipart/form-data">
        @csrf

        @if (session('error'))
            <div class="card" style="border-color:#fecaca;background:#fff1f2;color:#b91c1c;">{{ session('error') }}</div>
        @endif

        @if(($guestContext ?? null) !== null)
            <div class="card" style="margin-bottom: 16px; background: #eef2ff; border: 1px solid #c7d2fe;">
                <strong style="color: var(--primary);">Guest Mode</strong>
                <div style="font-size: 13px; margin-top: 6px; color:black">Data pengguna sudah terisi otomatis dari akun login guest.</div>
            </div>
            <input type="hidden" name="employeeName" value="{{ $guestContext['employeeName'] }}">
            <input type="hidden" name="employeeEmail" value="{{ $guestContext['employeeEmail'] }}">
            <input type="hidden" name="department" value="{{ $guestContext['department'] }}">
        @else
            <h3 style="color: var(--primary); margin-bottom: 14px;">Employee Information</h3>

            <div class="grid">
                <div class="form-group">
                    <label for="employeeName">Nama Karyawan *</label>
                    <input type="text" id="employeeName" name="employeeName" required value="{{ old('employeeName') }}">
                </div>
                <div class="form-group">
                    <label for="employeeEmail">Email *</label>
                    <input type="email" id="employeeEmail" name="employeeEmail" required value="{{ old('employeeEmail') }}">
                </div>
                <div class="form-group">
                    <label for="department">Departemen *</label>
                    <select id="department" name="department" required>
                        <option value="">Pilih...</option>
                        <option value="HR" @selected(old('department') === 'HR')>HR</option>
                        <option value="FIN" @selected(old('department') === 'FIN')>FIN</option>
                        <option value="IT" @selected(old('department') === 'IT')>IT</option>
                        <option value="OPS" @selected(old('department') === 'OPS')>OPS</option>
                    </select>
                </div>
            </div>
        @endif

        @if(count($form->fields_config ?? []) > 0)
            <h3 style="color: var(--primary); margin-top: 24px; margin-bottom: 14px;">Form Fields</h3>

            @foreach($form->fields_config ?? [] as $field)
                @php
                    $fieldId = $field['id'];
                    $fieldType = $field['type'];
                    $fieldLabel = $field['label'];
                    $fieldRequired = $field['required'] ?? false;
                    $fieldOptions = $field['options'] ?? [];
                    $fieldFormula = $field['formula'] ?? '';
                @endphp

                @if($fieldType === 'text')
                    <div class="form-group">
                        <label for="{{ $fieldId }}">{{ $fieldLabel }}{{ $fieldRequired ? ' *' : '' }}</label>
                        <input type="text" id="{{ $fieldId }}" name="{{ $fieldId }}" {{ $fieldRequired ? 'required' : '' }} value="{{ old($fieldId) }}">
                    </div>

                @elseif($fieldType === 'textarea')
                    <div class="form-group">
                        <label for="{{ $fieldId }}">{{ $fieldLabel }}{{ $fieldRequired ? ' *' : '' }}</label>
                        <textarea id="{{ $fieldId }}" name="{{ $fieldId }}" {{ $fieldRequired ? 'required' : '' }}>{{ old($fieldId) }}</textarea>
                    </div>

                @elseif($fieldType === 'number')
                    <div class="form-group">
                        <label for="{{ $fieldId }}">{{ $fieldLabel }}{{ $fieldRequired ? ' *' : '' }}</label>
                        <input type="number" id="{{ $fieldId }}" name="{{ $fieldId }}" {{ $fieldRequired ? 'required' : '' }} value="{{ old($fieldId) }}">
                    </div>

                @elseif($fieldType === 'email')
                    <div class="form-group">
                        <label for="{{ $fieldId }}">{{ $fieldLabel }}{{ $fieldRequired ? ' *' : '' }}</label>
                        <input type="email" id="{{ $fieldId }}" name="{{ $fieldId }}" {{ $fieldRequired ? 'required' : '' }} value="{{ old($fieldId) }}">
                    </div>

                @elseif($fieldType === 'date')
                    <div class="form-group">
                        <label for="{{ $fieldId }}">{{ $fieldLabel }}{{ $fieldRequired ? ' *' : '' }}</label>
                        <input type="date" id="{{ $fieldId }}" name="{{ $fieldId }}" {{ $fieldRequired ? 'required' : '' }} value="{{ old($fieldId) }}">
                    </div>

                @elseif($fieldType === 'dropdown')
                    <div class="form-group">
                        <label for="{{ $fieldId }}">{{ $fieldLabel }}{{ $fieldRequired ? ' *' : '' }}</label>
                        <select id="{{ $fieldId }}" name="{{ $fieldId }}" {{ $fieldRequired ? 'required' : '' }}>
                            <option value="">Select...</option>
                            @foreach($fieldOptions as $option)
                                <option value="{{ $option }}" {{ old($fieldId) === $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                @elseif($fieldType === 'radio')
                    <div class="form-group">
                        <label style="margin-bottom: 8px;">{{ $fieldLabel }}{{ $fieldRequired ? ' *' : '' }}</label>
                        <div style="display: flex; gap: 12px; margin-top: 6px;">
                            @foreach($fieldOptions as $option)
                                <label style="display: flex; align-items: center; gap: 6px; margin: 0;">
                                    <input type="radio" name="{{ $fieldId }}" value="{{ $option }}" {{ old($fieldId) === $option ? 'checked' : '' }} {{ $fieldRequired ? 'required' : '' }}>
                                    {{ $option }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                @elseif($fieldType === 'checkbox')
                    <div class="form-group">
                        <label style="margin-bottom: 8px;">{{ $fieldLabel }}{{ $fieldRequired ? ' *' : '' }}</label>
                        <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 6px;">
                            @foreach($fieldOptions as $option)
                                <label style="display: flex; align-items: center; gap: 6px; margin: 0;">
                                    <input type="checkbox" name="{{ $fieldId }}[]" value="{{ $option }}" {{ in_array($option, old($fieldId, [])) ? 'checked' : '' }}>
                                    {{ $option }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                @elseif($fieldType === 'file')
                    <div class="form-group">
                        <label for="{{ $fieldId }}">{{ $fieldLabel }}{{ $fieldRequired ? ' *' : '' }}</label>
                        <input type="file" id="{{ $fieldId }}" name="{{ $fieldId }}" {{ $fieldRequired ? 'required' : '' }}>
                    </div>

                @elseif($fieldType === 'calculation')
                    <div class="form-group">
                        <label>{{ $fieldLabel }}</label>
                        <div style="padding: 10px 12px; background: #dbeafe; border-radius: 8px; border: 1px solid #bfdbfe;">
                            <div style="font-size: 12px; color: #0038e0; margin-bottom: 4px;">Formula: {{ $fieldFormula }}</div>
                            <input type="text" readonly style="background: #fff; margin: 0; pointer-events: none;" value="Calculated value will appear here">
                        </div>
                    </div>

                @endif
            @endforeach
        @endif

        <div class="actions">
            <a href="{{ route('public.forms.index') }}" class="btn btn-outline">Back</a>
            <button type="submit" class="btn btn-primary">Submit Form</button>
        </div>
    </form>
    @endif
@endsection
