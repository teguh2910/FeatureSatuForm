@extends('feature-satu-form::public.layout')

@section('panel_title', $form->name)
@section('panel_subtitle', $form->description ?? '')

@section('content')

    @php
        $hasDependency = !empty($dependencyState['has_dependency'] ?? false);
        $dependencyVerified = !empty($dependencyVerified ?? false);
        $dependencyVerificationTracking = (string) ($dependencyVerification['tracking_id'] ?? old('dependency_tracking_id', ''));
    @endphp

    <!-- Breadcrumb -->
    <nav class="mb-4">
        <a href="{{ route('public.forms.index') }}" class="text-primary text-decoration-none small">
            <i class="ri-arrow-left-line me-1"></i> Back to Forms
        </a>
    </nav>

    <!-- Dependency Required -->
    @if($hasDependency && !$dependencyVerified)
        <div class="alert alert-warning border-0 mb-4" role="alert">
            <div class="d-flex align-items-start gap-2">
                <i class="ri-error-warning-line fs-5 mt-1"></i>
                <div>
                    <strong>Dependency Required</strong>
                    <p class="mb-0 mt-1 small">Form ini bergantung pada {{ $dependencyState['dependency_form']->form_code ?? 'dependency' }}. Masukkan nomor track dari form dependency yang sudah approved, lalu klik Verify.</p>
                    @if(!empty($dependencyState['message']))
                        <p class="mb-0 mt-1 small text-muted">{{ $dependencyState['message'] }}</p>
                    @endif
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('public.forms.verify-dependency', $form->id) }}" class="mb-3">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-semibold text-muted text-uppercase" for="dependency_tracking_id">Dependency Track Number</label>
                <input id="dependency_tracking_id" name="dependency_tracking_id" value="{{ old('dependency_tracking_id', $dependencyVerificationTracking) }}"
                       class="form-control mono" placeholder="TRK-XXXXXXXXXX" required>
                @error('dependency_tracking_id')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('public.forms.index') }}" class="btn btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i> Back
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-shield-check-line me-1"></i> Verify
                </button>
            </div>
        </form>

    @else

        <!-- Dependency Verified Notice -->
        @if($hasDependency && $dependencyVerified)
            <div class="alert alert-success border-0 d-flex align-items-center gap-2 mb-4">
                <i class="ri-checkbox-circle-line fs-5"></i>
                <div>
                    <strong>Dependency Verified</strong>
                    <span class="ms-2 mono">{{ $dependencyVerification['tracking_id'] ?? 'Dependency sudah diverifikasi.' }}</span>
                </div>
            </div>
        @endif

        <!-- Error Flash -->
        @if(session('error'))
            <div class="alert alert-danger border-0 mb-4">{{ session('error') }}</div>
        @endif

        <!-- Submission Form -->
        <form method="POST" action="{{ route('public.forms.store', $form->id) }}" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf

            <!-- Guest / Employee Info -->
            @if(($guestContext ?? null) !== null)
                <input type="hidden" name="employeeName" value="{{ $guestContext['employeeName'] }}">
                <input type="hidden" name="employeeEmail" value="{{ $guestContext['employeeEmail'] }}">
                <input type="hidden" name="department" value="{{ $guestContext['department'] }}">

                <div class="alert alert-info border-0 d-flex align-items-center gap-2 mb-4">
                    <i class="ri-user-follow-line fs-5"></i>
                    <div>
                        <strong>Guest Mode</strong>
                        <p class="mb-0 small mt-1">Data pengguna sudah terisi otomatis dari akun login guest.</p>
                    </div>
                </div>
            @else
                <h5 class="text-uppercase text-primary fw-bold mb-3">
                    <i class="ri-user-line me-2"></i>Employee Information
                </h5>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted text-uppercase" for="employeeName">Nama Karyawan *</label>
                        <input type="text" id="employeeName" name="employeeName" class="form-control @error('employeeName') is-invalid @enderror" required value="{{ old('employeeName') }}">
                        @error('employeeName')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted text-uppercase" for="employeeEmail">Email *</label>
                        <input type="email" id="employeeEmail" name="employeeEmail" class="form-control @error('employeeEmail') is-invalid @enderror" required value="{{ old('employeeEmail') }}">
                        @error('employeeEmail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted text-uppercase" for="department">Departemen *</label>
                        <select id="department" name="department" class="form-select @error('department') is-invalid @enderror" required>
                            <option value="">Pilih...</option>
                            <option value="HR" @selected(old('department') === 'HR')>HR</option>
                            <option value="FIN" @selected(old('department') === 'FIN')>FIN</option>
                            <option value="IT" @selected(old('department') === 'IT')>IT</option>
                            <option value="OPS" @selected(old('department') === 'OPS')>OPS</option>
                        </select>
                        @error('department')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            @endif

            <!-- Form Fields -->
            @if(count($form->fields_config ?? []) > 0)
                <h5 class="text-uppercase text-primary fw-bold mb-3">
                    <i class="ri-file-text-line me-2"></i>Form Fields
                </h5>

                <div class="mb-4">
                    @foreach($form->fields_config ?? [] as $field)
                        @php
                            $fieldId = $field['id'];
                            $fieldType = $field['type'];
                            $fieldLabel = $field['label'];
                            $fieldRequired = $field['required'] ?? false;
                            $fieldOptions = $field['options'] ?? [];
                            $fieldFormula = $field['formula'] ?? '';
                        @endphp

                        <div class="mb-3">
                            @if($fieldType === 'text')
                                <label class="form-label small fw-semibold text-muted text-uppercase" for="{{ $fieldId }}">{{ $fieldLabel }}{{ $fieldRequired ? ' *' : '' }}</label>
                                <input type="text" id="{{ $fieldId }}" name="{{ $fieldId }}" class="form-control" {{ $fieldRequired ? 'required' : '' }} value="{{ old($fieldId) }}">

                            @elseif($fieldType === 'textarea')
                                <label class="form-label small fw-semibold text-muted text-uppercase" for="{{ $fieldId }}">{{ $fieldLabel }}{{ $fieldRequired ? ' *' : '' }}</label>
                                <textarea id="{{ $fieldId }}" name="{{ $fieldId }}" class="form-control" rows="3" {{ $fieldRequired ? 'required' : '' }}>{{ old($fieldId) }}</textarea>

                            @elseif($fieldType === 'number')
                                <label class="form-label small fw-semibold text-muted text-uppercase" for="{{ $fieldId }}">{{ $fieldLabel }}{{ $fieldRequired ? ' *' : '' }}</label>
                                <input type="number" id="{{ $fieldId }}" name="{{ $fieldId }}" class="form-control" {{ $fieldRequired ? 'required' : '' }} value="{{ old($fieldId) }}">

                            @elseif($fieldType === 'email')
                                <label class="form-label small fw-semibold text-muted text-uppercase" for="{{ $fieldId }}">{{ $fieldLabel }}{{ $fieldRequired ? ' *' : '' }}</label>
                                <input type="email" id="{{ $fieldId }}" name="{{ $fieldId }}" class="form-control" {{ $fieldRequired ? 'required' : '' }} value="{{ old($fieldId) }}">

                            @elseif($fieldType === 'date')
                                <label class="form-label small fw-semibold text-muted text-uppercase" for="{{ $fieldId }}">{{ $fieldLabel }}{{ $fieldRequired ? ' *' : '' }}</label>
                                <input type="date" id="{{ $fieldId }}" name="{{ $fieldId }}" class="form-control" {{ $fieldRequired ? 'required' : '' }} value="{{ old($fieldId) }}">

                            @elseif($fieldType === 'dropdown')
                                <label class="form-label small fw-semibold text-muted text-uppercase" for="{{ $fieldId }}">{{ $fieldLabel }}{{ $fieldRequired ? ' *' : '' }}</label>
                                <select id="{{ $fieldId }}" name="{{ $fieldId }}" class="form-select" {{ $fieldRequired ? 'required' : '' }}>
                                    <option value="">Select...</option>
                                    @foreach($fieldOptions as $option)
                                        <option value="{{ $option }}" {{ old($fieldId) === $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>

                            @elseif($fieldType === 'radio')
                                <label class="form-label small fw-semibold text-muted text-uppercase d-block mb-2">{{ $fieldLabel }}{{ $fieldRequired ? ' *' : '' }}</label>
                                <div class="d-flex flex-column gap-2">
                                    @foreach($fieldOptions as $option)
                                        <div class="form-check">
                                            <input type="radio" id="{{ $fieldId }}_{{ $loop->index }}" name="{{ $fieldId }}" value="{{ $option }}" class="form-check-input" {{ old($fieldId) === $option ? 'checked' : '' }} {{ $fieldRequired ? 'required' : '' }}>
                                            <label class="form-check-label" for="{{ $fieldId }}_{{ $loop->index }}">{{ $option }}</label>
                                        </div>
                                    @endforeach
                                </div>

                            @elseif($fieldType === 'checkbox')
                                <label class="form-label small fw-semibold text-muted text-uppercase d-block mb-2">{{ $fieldLabel }}{{ $fieldRequired ? ' *' : '' }}</label>
                                <div class="d-flex flex-column gap-2">
                                    @foreach($fieldOptions as $option)
                                        <div class="form-check">
                                            <input type="checkbox" id="{{ $fieldId }}_{{ $loop->index }}" name="{{ $fieldId }}[]" value="{{ $option }}" class="form-check-input" {{ in_array($option, old($fieldId, [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="{{ $fieldId }}_{{ $loop->index }}">{{ $option }}</label>
                                        </div>
                                    @endforeach
                                </div>

                            @elseif($fieldType === 'file')
                                <label class="form-label small fw-semibold text-muted text-uppercase" for="{{ $fieldId }}">{{ $fieldLabel }}{{ $fieldRequired ? ' *' : '' }}</label>
                                <input type="file" id="{{ $fieldId }}" name="{{ $fieldId }}" class="form-control" {{ $fieldRequired ? 'required' : '' }}>
                                @error($fieldId)
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror

                            @elseif($fieldType === 'calculation')
                                <label class="form-label small fw-semibold text-muted text-uppercase">{{ $fieldLabel }}</label>
                                <div class="p-3 rounded-2" style="background: #dbeafe; border: 1px solid #bfdbfe;">
                                    <div class="small mb-2" style="color: var(--fs-accent);">
                                        <i class="ri-function-line me-1"></i>Formula: {{ $fieldFormula }}
                                    </div>
                                    <input type="text" readonly class="form-control-plaintext bg-white" value="Calculated value will appear here">
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Actions -->
            <div class="d-flex gap-2 border-top pt-4">
                <a href="{{ route('public.forms.index') }}" class="btn btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i> Back
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-send-plane-line me-1"></i> Submit Form
                </button>
            </div>
        </form>
    @endif
@endsection
