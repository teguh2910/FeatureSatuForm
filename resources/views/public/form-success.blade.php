@extends('feature-satu-form::public.layout')

@section('title', 'Form Submitted')

@section('content')
    <div style="text-align: center;">
        <div class="success-icon">✓</div>

        <h1 class="headline">Form Submitted Successfully!</h1>
        <p class="sub">Your submission has been received and recorded.</p>

        <div class="card" style="margin-top: 20px;">
            <div style="margin-bottom: 12px;">
                <strong style="color: var(--primary);">Your Tracking ID</strong>
                <div class="tracking-id">{{ $submission->tracking_id }}</div>
                <p style="font-size: 12px; color: #64748b; margin: 8px 0 0;">Save this ID to track your submission status</p>
            </div>

            <div style="padding: 12px; background: var(--light); border-radius: 8px; font-size: 13px; color: var(--text);">
                <strong>Form:</strong> {{ $submission->form_type }}<br>
                <strong>Employee:</strong> {{ $submission->employee_name }}<br>
                <strong>Email:</strong> {{ $submission->employee_email }}<br>
                <strong>Status:</strong> <span class="badge badge-warning">{{ ucfirst($submission->status) }}</span><br>
                <strong>Submitted:</strong> {{ $submission->submitted_at->format('d M Y H:i') }}
            </div>
        </div>

        <div class="actions" style="justify-content: center;">
            <a href="{{ route('public.forms.track', ['tracking_id' => $submission->tracking_id]) }}" class="btn btn-outline">Track Approval</a>
            <a href="{{ route('public.forms.index') }}" class="btn btn-primary">Back to Forms</a>
        </div>
    </div>
@endsection
