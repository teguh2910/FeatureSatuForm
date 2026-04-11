@extends('feature-satu-form::public.layout')

@section('panel_title', 'Welcome to SATU Form')
@section('panel_subtitle', 'Sistem Form Terpadu — Isi, Ajukan, dan Lacak Form dengan Mudah.')

@section('content')
    <div class="text-center py-4">
        <div class="mb-4">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background: #dbeafe;">
                <i class="ri-file-list-3-line text-primary" style="font-size: 3rem;"></i>
            </div>
        </div>

        <h4 class="text-primary mb-2">Selamat Datang di SATU Form!</h4>
        <p class="text-muted mb-4">Sistem Form Terpadu untuk mengajukan dan melacak berbagai keperluan kantor.</p>

        <div class="row g-3 justify-content-center mb-4">
            <div class="col-md-4">
                <div class="card border-0 rounded-3 h-100" style="background: #f0f2f8;">
                    <div class="card-body text-start">
                        <div class="mb-2"><i class="ri-edit-3-line text-primary fs-3"></i></div>
                        <h6 class="fw-bold">Isi Form</h6>
                        <p class="small text-muted mb-0">Pilih form yang tersedia dan isi data yang diperlukan.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 rounded-3 h-100" style="background: #f0f2f8;">
                    <div class="card-body text-start">
                        <div class="mb-2"><i class="ri-send-plane-line text-primary fs-3"></i></div>
                        <h6 class="fw-bold">Ajukan</h6>
                        <p class="small text-muted mb-0">Kirimkan pengajuan dan dapatkan tracking ID.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 rounded-3 h-100" style="background: #f0f2f8;">
                    <div class="card-body text-start">
                        <div class="mb-2"><i class="ri-search-line text-primary fs-3"></i></div>
                        <h6 class="fw-bold">Lacak</h6>
                        <p class="small text-muted mb-0">Pantau status approval dengan tracking ID.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
            <a href="{{ route('public.forms.index') }}" class="btn btn-primary btn-lg">
                <i class="ri-file-list-3-line me-2"></i> Lihat Form Tersedia
            </a>
            <a href="{{ route('public.forms.track') }}" class="btn btn-outline-primary btn-lg">
                <i class="ri-search-line me-2"></i> Lacak Submission
            </a>
        </div>
    </div>
@endsection
