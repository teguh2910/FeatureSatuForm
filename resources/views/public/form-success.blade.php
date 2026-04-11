@extends('feature-satu-form::public.layout')

@section('panel_title', 'Submission Received')
@section('panel_subtitle', 'Form berhasil diajukan.')

@section('content')
    <div class="text-center py-4">
        <!-- Success Icon -->
        <div class="mb-4">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background: #dcfce7;">
                <i class="ri-checkbox-circle-line text-success" style="font-size: 3rem;"></i>
            </div>
        </div>

        <h4 class="text-primary mb-2">Form Submitted Successfully!</h4>
        <p class="text-muted mb-1">Pengajuan Anda telah diterima dan sedang dalam proses review.</p>
        <p class="text-muted small">Simpan tracking ID di bawah ini untuk melacak status pengajuan.</p>

        <!-- Tracking ID -->
        <div class="my-4 p-3 rounded-3 mx-auto" style="max-width: 400px; background: var(--fs-light); border: 1px dashed var(--fs-border);">
            <div class="small text-muted text-uppercase mb-1">Tracking ID</div>
            <div class="fs-5 fw-bold text-primary mono">{{ $trackingId }}</div>
        </div>

        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center mt-4">
            <a href="{{ route('public.forms.track') }}" class="btn btn-outline-primary">
                <i class="ri-search-line me-1"></i> Track Submission
            </a>
            <a href="{{ route('public.forms.index') }}" class="btn btn-primary">
                <i class="ri-home-4-line me-1"></i> Back to Forms
            </a>
        </div>
    </div>
@endsection
