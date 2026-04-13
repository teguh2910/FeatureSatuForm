@extends('feature-satu-form::public.layout')

@section('title', 'Track Approval')

@section('content')
    <div class="top-links">
        <h1 class="headline">Track Approval</h1>
        <a href="{{ route('public.forms.index') }}">Back to Forms</a>
    </div>

    <p class="sub">Masukkan tracking ID untuk melihat status approval form.</p>

    <form method="GET" action="{{ route('public.forms.track') }}" class="card" style="margin-bottom: 16px;">
        <div class="grid" style="grid-template-columns: 1fr auto; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="tracking_id">Tracking ID</label>
                <input id="tracking_id" name="tracking_id" value="{{ $trackingId }}" placeholder="TRK-XXXXXXXXXX" required>
            </div>
            <button type="submit" class="btn btn-primary">Check Status</button>
        </div>
    </form>

    @if($trackingId !== '' && !$submission)
        <div class="card" style="border-color:#fecaca;background:#fff1f2;color:#b91c1c;">
            Tracking ID tidak ditemukan.
        </div>
    @endif

    @if($submission)
        <div class="card">
            <h3 style="margin-top:0;color:var(--primary);">Submission Summary</h3>
            <div style="line-height:1.8; font-size:14px;">
                <div style="color:var(--text);"><strong>Tracking ID:</strong> {{ $submission->tracking_id }}</div>
                <div style="color:var(--text);"><strong>Form:</strong> {{ $submission->form_type }}</div>
                <div style="color:var(--text);"><strong>Employee:</strong> {{ $submission->employee_name }}</div>
                <div style="color:var(--text);"><strong>Email:</strong> {{ $submission->employee_email }}</div>
                <div style="color:var(--text);"><strong>Department:</strong> {{ $submission->department }}</div>
                <div style="color:var(--text);"><strong>Current Status:</strong> <span class="badge badge-info">{{ strtoupper($submission->status) }}</span></div>
                <div style="color:var(--text);"><strong>Submitted At:</strong> {{ optional($submission->submitted_at)->format('d M Y H:i') }}</div>
            </div>
        </div>

        <div class="card">
            <h3 style="margin-top:0;color:var(--primary);">Approval Flow</h3>
            @php
                $flow = is_array($submission->approval_flow_snapshot) ? $submission->approval_flow_snapshot : [];
                $history = is_array($submission->approval_history) ? $submission->approval_history : [];
            @endphp

            @if(count($flow) === 0)
                <p class="sub" style="margin:0;">No approval steps configured.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th style="width:70px;">Step</th>
                            <th>Name</th>
                            <th style="width:150px;">Role</th>
                            <th style="width:150px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($flow as $step)
                            <tr>
                                <td>{{ $step['order'] ?? '-' }}</td>
                                <td>{{ $step['name'] ?? '-' }}</td>
                                <td>{{ $step['role'] ?? '-' }}</td>
                                <td>
                                    @php($stepStatus = $step['status'] ?? 'pending')
                                    @if($stepStatus === 'approved')
                                        <span class="badge badge-success">APPROVED</span>
                                    @elseif($stepStatus === 'rejected')
                                        <span class="badge" style="background:#fee2e2;color:#b91c1c;">REJECTED</span>
                                    @else
                                        <span class="badge badge-warning">PENDING</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if(count($history) > 0)
                <h4 style="margin:16px 0 8px;color:var(--primary);">History</h4>
                <table>
                    <thead>
                        <tr>
                            <th style="width:180px;">Timestamp</th>
                            <th style="width:120px;">Actor</th>
                            <th style="width:140px;">Action</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(array_reverse($history) as $event)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($event['timestamp'] ?? now())->format('d M Y H:i') }}</td>
                                <td>{{ $event['actor'] ?? '-' }}</td>
                                <td>{{ strtoupper($event['action'] ?? '-') }}</td>
                                <td>{{ $event['note'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endif
@endsection
