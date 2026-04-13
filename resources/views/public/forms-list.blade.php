@extends('feature-satu-form::public.layout')

@section('panel_title', 'Available Forms')
@section('panel_subtitle', 'Daftar form yang tersedia untuk diisi. Klik "Isi Form" untuk memulai.')
@section('content')

    @if($forms->isEmpty())
        <div class="text-center py-5">
            <i class="ri-file-search-line text-muted opacity-25" style="font-size: 4rem;"></i>
            <h5 class="mt-3 text-muted">Tidak ada form yang tersedia saat ini.</h5>
            <p class="text-muted small">Silakan hubungi administrator untuk informasi lebih lanjut.</p>
        </div>
    @else
        <div class="table-responsive">
            <table id="formsTable" class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th class="text-uppercase" style="width: 50px;">No</th>
                        <th class="text-uppercase">Nama Form</th>
                        <th class="text-uppercase" style="width: 100px;">Dept</th>
                        <th class="text-uppercase" style="width: 80px;">Fields</th>
                        <th class="text-uppercase" style="width: 120px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($forms as $index => $form)
                        @php($dependencyState = $form->dependency_state ?? ['is_blocked' => false, 'message' => null])
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-semibold text-primary">{{ $form->name }}</div>
                                <div class="small text-muted">{{ $form->description ?? 'No description' }}</div>
                                @if(!empty($form->form_code))
                                    <div class="small text-muted mt-1">
                                        <i class="ri-barcode-line me-1"></i>Code: {{ $form->form_code }}
                                    </div>
                                @endif
                                @if(!empty($form->dependency_form_code))
                                    <div class="small mt-1" style="color: {{ ($dependencyState['is_blocked'] ?? false) ? 'var(--fs-danger)' : 'var(--fs-success)' }};">
                                        <i class="ri-link me-1"></i>
                                        Depends on {{ $form->dependency_form_code }}
                                        @if(($dependencyState['is_blocked'] ?? false) && !empty($dependencyState['message']))
                                            <span> — {{ $dependencyState['message'] }}</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td><span class="badge bg-light text-dark">{{ $form->department ?? '-' }}</span></td>
                            <td class="text-center"><span class="badge bg-secondary">{{ count($form->fields_config ?? []) }}</span></td>
                            <td>
                                <a href="{{ route('public.forms.show', $form->id) }}"
                                   class="btn btn-sm {{ !empty($form->dependency_form_code) ? 'btn-outline-primary' : 'btn-primary' }}">
                                    @if(!empty($form->dependency_form_code))
                                        <i class="ri-shield-check-line me-1"></i>Verify
                                    @else
                                        <i class="ri-edit-line me-1"></i>Isi Form
                                    @endif
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
