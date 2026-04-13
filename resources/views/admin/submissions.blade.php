@extends('feature-satu-form::admin.layout')

@section('title', 'Submissions')
@section('active_nav', 'submissions')

@section('topbar_actions')
    <a href="{{ url('/') }}" class="btn btn-outline-light btn-sm">
        <i class="ri-external-link-line me-1"></i> User Form
    </a>
@endsection

@section('page_styles')
    <style>
        .filter-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto auto;
            gap: 12px;
            align-items: end;
        }
        @media (max-width: 992px) {
            .filter-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 576px) {
            .filter-grid { grid-template-columns: 1fr; }
        }
        .mono { font-family: "JetBrains Mono", "Segoe UI Mono", monospace; }
    </style>
@endsection

@section('content')

    <!-- Filters Card -->
    <div class="card border-0 rounded-3 mb-4">
        <div class="card-body">
            <form class="filter-grid" method="GET" action="{{ route('admin.submissions.index') }}">
                <div>
                    <label class="form-label small fw-semibold text-muted text-uppercase" for="q">Search</label>
                    <input id="q" name="q" value="{{ $filters['q'] }}" class="form-control" placeholder="Tracking ID, nama, email, atau form">
                </div>
                <div>
                    <label class="form-label small fw-semibold text-muted text-uppercase" for="status">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">All</option>
                        <option value="in_review" @selected($filters['status'] === 'in_review')>In Review</option>
                        <option value="approved" @selected($filters['status'] === 'approved')>Approved</option>
                        <option value="rejected" @selected($filters['status'] === 'rejected')>Rejected</option>
                    </select>
                </div>
                <div>
                    <label class="form-label small fw-semibold text-muted text-uppercase" for="department">Department</label>
                    <select id="department" name="department" class="form-select" @if(!empty($userDept)) disabled @endif>
                        <option value="">All</option>
                        <option value="HR" @selected($filters['department'] === 'HR')>HR</option>
                        <option value="FIN" @selected($filters['department'] === 'FIN')>FIN</option>
                        <option value="IT" @selected($filters['department'] === 'IT')>IT</option>
                        <option value="OPS" @selected($filters['department'] === 'OPS')>OPS</option>
                    </select>
                    @if(!empty($userDept))
                        <input type="hidden" name="department" value="{{ $userDept }}">
                    @endif
                </div>
                <div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ri-filter-2 me-1"></i> Apply
                    </button>
                </div>
                <div>
                    <a href="{{ route('admin.submissions.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="ri-refresh-line me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Submissions Table -->
    <div class="card border-0 rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-uppercase" style="font-size: 11px;">Tracking ID</th>
                            <th class="text-uppercase" style="font-size: 11px;">Employee</th>
                            <th class="text-uppercase" style="font-size: 11px;">Dept</th>
                            <th class="text-uppercase" style="font-size: 11px;">Form</th>
                            <th class="text-uppercase" style="font-size: 11px;">Step</th>
                            <th class="text-uppercase" style="font-size: 11px;">Status</th>
                            <th class="text-uppercase" style="font-size: 11px;">Submitted</th>
                            <th class="text-uppercase" style="font-size: 11px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $submission)
                            <tr>
                                <td><span class="badge bg-dark mono">{{ $submission->tracking_id }}</span></td>
                                <td>
                                    <div class="fw-semibold">{{ $submission->employee_name }}</div>
                                    <div class="small text-muted">{{ $submission->employee_email }}</div>
                                </td>
                                <td><span class="badge bg-light text-dark">{{ $submission->department }}</span></td>
                                <td>{{ $submission->form_type }}</td>
                                <td>
                                    @php($flowTotal = is_array($submission->approval_flow_snapshot) ? count($submission->approval_flow_snapshot) : 0)
                                    @if($flowTotal > 0)
                                        <span class="badge bg-primary">{{ $submission->current_approval_step }}/{{ $flowTotal }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($submission->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($submission->status === 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-warning text-dark">In Review</span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ optional($submission->submitted_at)->format('d M Y H:i') }}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <form method="POST" action="{{ route('admin.submissions.status', $submission->tracking_id) }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="ri-check-line"></i><span class="d-none d-sm-inline ms-1">Approve</span>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.submissions.status', $submission->tracking_id) }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="ri-close-line"></i><span class="d-none d-sm-inline ms-1">Reject</span>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.submissions.status', $submission->tracking_id) }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="in_review">
                                            <button type="submit" class="btn btn-outline-secondary btn-sm">
                                                <i class="ri-eye-line"></i><span class="d-none d-sm-inline ms-1">Review</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="ri-inbox-2-line fs-1 d-block mb-2 opacity-25"></i>
                                    <div>Belum ada submission.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($submissions->hasPages())
            <div class="card-footer bg-white border-0">
                {{ $submissions->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
