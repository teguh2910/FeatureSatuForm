@extends('feature-satu-form::admin.layout')

@section('title', 'Dashboard')

@section('active_nav', 'dashboard')

@section('page_styles')
    .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 12px; margin-bottom: 16px; }
    .title { font-size: 13px; color: #64748b; margin-bottom: 8px; }
    .value { font-size: 32px; font-weight: 800; color: var(--primary); }
    .badge { display: inline-flex; padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 700; }
    .status-approved { background: #dcfce7; color: #16a34a; }
    .status-rejected { background: #fee2e2; color: #dc2626; }
    .status-in_review { background: #fef3c7; color: #92400e; }
    .mono { font-family: "JetBrains Mono", monospace; }
@endsection

@section('content')
    <section class="grid">
        <div class="card">
            <div class="title">Total Forms</div>
            <div class="value">{{ $stats['totalForms'] }}</div>
        </div>
        <div class="card">
            <div class="title">Total Submissions</div>
            <div class="value">{{ $stats['totalSubmissions'] }}</div>
        </div>
        <div class="card">
            <div class="title">Pending Review</div>
            <div class="value" style="color: var(--warn);">{{ $stats['pendingSubmissions'] }}</div>
        </div>
        <div class="card">
            <div class="title">Approved</div>
            <div class="value" style="color: var(--success);">{{ $stats['approvedSubmissions'] }}</div>
        </div>
    </section>

    <section class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
            <h3 style="margin:0;color:var(--primary);">Recent Submissions</h3>
            <a href="{{ route('admin.submissions.index') }}" class="btn btn-primary">View All</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Form</th>
                    <th>Employee</th>
                    <th>Status</th>
                    <th>Submitted</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentSubmissions as $submission)
                    <tr>
                        <td class="mono">{{ $submission->tracking_id }}</td>
                        <td>{{ $submission->form_type }}</td>
                        <td>{{ $submission->employee_name }}</td>
                        <td><span class="badge status-{{ $submission->status }}">{{ strtoupper($submission->status) }}</span></td>
                        <td>{{ optional($submission->submitted_at)->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center;color:#64748b;">Belum ada submission.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
