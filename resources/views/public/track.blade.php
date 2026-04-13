@extends('feature-satu-form::public.layout')

@section('panel_title', 'Track Approval')
@section('panel_subtitle', 'Masukkan tracking ID untuk melihat status approval form.')

@section('content')

    <!-- Search Form -->
    <form method="GET" action="{{ route('public.forms.track') }}" class="card border-0 rounded-3 mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col">
                    <label class="form-label small fw-semibold text-muted text-uppercase" for="tracking_id">Tracking ID</label>
                    <input id="tracking_id" name="tracking_id" value="{{ $trackingId }}" class="form-control mono" placeholder="TRK-XXXXXXXXXX" required>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-search-line me-1"></i> Check Status
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Not Found -->
    @if($trackingId !== '' && !$submission)
        <div class="alert alert-danger border-0 d-flex align-items-center gap-2">
            <i class="ri-error-warning-line fs-5"></i>
            <div>Tracking ID tidak ditemukan.</div>
        </div>
    @endif

    <!-- Submission Summary -->
    @if($submission)
        <div class="card border-0 rounded-3 mb-4">
            <div class="card-header bg-white border-0 rounded-3 py-3">
                <h5 class="mb-0 text-primary"><i class="ri-file-list-3-line me-2"></i>Submission Summary</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="small text-muted text-uppercase">Tracking ID</div>
                        <div class="fw-semibold mono">{{ $submission->tracking_id }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted text-uppercase">Status</div>
                        <div class="fw-semibold">
                            @if($submission->status === 'approved')
                                <span class="badge bg-success">{{ strtoupper($submission->status) }}</span>
                            @elseif($submission->status === 'rejected')
                                <span class="badge bg-danger">{{ strtoupper($submission->status) }}</span>
                            @else
                                <span class="badge bg-warning text-dark">{{ strtoupper($submission->status) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted text-uppercase">Form</div>
                        <div class="fw-semibold">{{ $submission->form_type }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted text-uppercase">Submitted</div>
                        <div class="fw-semibold">{{ optional($submission->submitted_at)->format('d M Y H:i') }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted text-uppercase">Employee</div>
                        <div>{{ $submission->employee_name }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted text-uppercase">Department</div>
                        <div>{{ $submission->department }}</div>
                    </div>
                    <div class="col-12">
                        <div class="small text-muted text-uppercase">Email</div>
                        <div>{{ $submission->employee_email }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval Flow -->
        <div class="card border-0 rounded-3">
            <div class="card-header bg-white border-0 rounded-3 py-3">
                <h5 class="mb-0 text-primary"><i class="ri-git-branch-line me-2"></i>Approval Flow</h5>
            </div>
            <div class="card-body p-0">
                @php
                    $flow = is_array($submission->approval_flow_snapshot) ? $submission->approval_flow_snapshot : [];
                    $history = is_array($submission->approval_history) ? $submission->approval_history : [];
                @endphp

                @if(count($flow) === 0)
                    <div class="p-4 text-muted small">No approval steps configured.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-uppercase" style="font-size: 11px; width: 70px;">Step</th>
                                    <th class="text-uppercase" style="font-size: 11px;">Name</th>
                                    <th class="text-uppercase" style="font-size: 11px; width: 140px;">Role</th>
                                    <th class="text-uppercase" style="font-size: 11px; width: 140px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($flow as $step)
                                    @php($stepStatus = $step['status'] ?? 'pending')
                                    <tr>
                                        <td>{{ $step['order'] ?? '-' }}</td>
                                        <td class="fw-semibold">{{ $step['name'] ?? '-' }}</td>
                                        <td><span class="badge bg-light text-dark">{{ $step['role'] ?? '-' }}</span></td>
                                        <td>
                                            @if($stepStatus === 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($stepStatus === 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if(count($history) > 0)
                    <div class="px-4 pt-4 pb-2">
                        <h6 class="text-uppercase text-muted fw-bold mb-3">
                            <i class="ri-history-line me-1"></i>History
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-uppercase" style="font-size: 10px; width: 170px;">Timestamp</th>
                                        <th class="text-uppercase" style="font-size: 10px; width: 120px;">Actor</th>
                                        <th class="text-uppercase" style="font-size: 10px; width: 130px;">Action</th>
                                        <th class="text-uppercase" style="font-size: 10px;">Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(array_reverse($history) as $event)
                                        <tr>
                                            <td class="small text-muted">{{ \Carbon\Carbon::parse($event['timestamp'] ?? now())->format('d M Y H:i') }}</td>
                                            <td><span class="badge bg-secondary">{{ $event['actor'] ?? '-' }}</span></td>
                                            <td>
                                                @if(($event['action'] ?? '') === 'approved')
                                                    <span class="badge bg-success">{{ strtoupper($event['action'] ?? '-') }}</span>
                                                @elseif(($event['action'] ?? '') === 'rejected')
                                                    <span class="badge bg-danger">{{ strtoupper($event['action'] ?? '-') }}</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">{{ strtoupper($event['action'] ?? '-') }}</span>
                                                @endif
                                            </td>
                                            <td class="small text-muted">{{ $event['note'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection
