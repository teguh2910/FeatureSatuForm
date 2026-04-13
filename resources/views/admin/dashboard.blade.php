@extends('feature-satu-form::admin.layout')

@section('title', 'Dashboard')
@section('active_nav', 'dashboard')

@section('page_styles')
    <style>
        .stat-card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 16px rgba(0, 26, 114, 0.08);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 26, 114, 0.12);
        }
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--fs-primary);
            line-height: 1;
        }
        .stat-label {
            font-size: 0.813rem;
            color: #64748b;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 4px;
        }
        .mono { font-family: "JetBrains Mono", "Segoe UI Mono", monospace; }
    </style>
@endsection

@section('content')
    <!-- Stats Grid -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-value">{{ $stats['totalForms'] }}</div>
                        <div class="stat-label">Total Forms</div>
                    </div>
                    <div class="fs-2 text-primary opacity-50">
                        <i class="ri-file-list-3-line"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-value">{{ $stats['totalSubmissions'] }}</div>
                        <div class="stat-label">Total Submissions</div>
                    </div>
                    <div class="fs-2 text-info opacity-50">
                        <i class="ri-inbox-archive-line"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-value" style="color: var(--fs-warn);">{{ $stats['pendingSubmissions'] }}</div>
                        <div class="stat-label">Pending Review</div>
                    </div>
                    <div class="fs-2 text-warning opacity-50">
                        <i class="ri-time-line"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-value" style="color: var(--fs-success);">{{ $stats['approvedSubmissions'] }}</div>
                        <div class="stat-label">Approved</div>
                    </div>
                    <div class="fs-2 text-success opacity-50">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Submissions -->
    <div class="card border-0 rounded-3">
        <div class="card-header bg-white border-0 rounded-3 d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 text-primary">
                <i class="ri-history-line me-2"></i>Recent Submissions
            </h5>
            <a href="{{ route('admin.submissions.index') }}" class="btn btn-outline-primary btn-sm">
                View All <i class="ri-arrow-right-s-line"></i>
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-uppercase" style="font-size: 11px;">Tracking ID</th>
                            <th class="text-uppercase" style="font-size: 11px;">Form</th>
                            <th class="text-uppercase" style="font-size: 11px;">Employee</th>
                            <th class="text-uppercase" style="font-size: 11px;">Status</th>
                            <th class="text-uppercase" style="font-size: 11px;">Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSubmissions as $submission)
                            <tr>
                                <td><span class="badge badge-dark mono">{{ $submission->tracking_id }}</span></td>
                                <td>{{ $submission->form_type }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $submission->employee_name }}</div>
                                    <div class="text-muted small">{{ $submission->employee_email }}</div>
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="ri-inbox-2-line fs-2 d-block mb-2 opacity-25"></i>
                                    Belum ada submission.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
