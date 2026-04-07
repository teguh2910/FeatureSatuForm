@extends('feature-satu-form::admin.layout')

@section('title', 'Submissions')

@section('active_nav', 'submissions')

@section('topbar_actions')
    <a href="{{ url('/') }}" class="btn btn-outline" style="text-decoration:none;display:inline-flex;align-items:center;">User Form</a>
@endsection

@section('page_styles')
    .filters {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr auto auto;
        gap: 10px;
        align-items: end;
    }

    .badge {
        display: inline-flex;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
    }

    .status-approved { background: #dcfce7; color: var(--success); }
    .status-rejected { background: #fee2e2; color: var(--danger); }
    .status-in_review { background: #fef3c7; color: #92400e; }
    .mono { font-family: "JetBrains Mono", monospace; }
    .actions { display: flex; gap: 6px; flex-wrap: wrap; }

    @media (max-width: 980px) {
        .filters { grid-template-columns: 1fr; }
        table, thead, tbody, tr, th, td { display: block; }
        thead { display: none; }
        tr {
            margin-bottom: 10px;
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
        }
        td { border-bottom: 1px solid #eef2ff; }
        td::before {
            content: attr(data-label);
            display: block;
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 4px;
            font-weight: 700;
        }
    }
@endsection

@section('content')
    <section class="card">
        <form class="filters" method="GET" action="{{ route('admin.submissions.index') }}">
            <div>
                <label for="q">Search</label>
                <input id="q" name="q" value="{{ $filters['q'] }}" placeholder="Tracking ID, nama, email, atau form">
            </div>
            <div>
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">All</option>
                    <option value="in_review" @selected($filters['status'] === 'in_review')>In Review</option>
                    <option value="approved" @selected($filters['status'] === 'approved')>Approved</option>
                    <option value="rejected" @selected($filters['status'] === 'rejected')>Rejected</option>
                </select>
            </div>
            <div>
                <label for="department">Department</label>
                <select id="department" name="department" @if(!empty($userDept)) disabled @endif>
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
            <button type="submit" class="btn btn-primary">Apply</button>
            <a href="{{ route('admin.submissions.index') }}" class="btn btn-outline" style="text-decoration:none;display:inline-flex;align-items:center;">Reset</a>
        </form>
    </section>

    <section class="card" style="overflow:auto;">
        <table>
            <thead>
                <tr>
                    <th>Tracking ID</th>
                    <th>Employee</th>
                    <th>Department</th>
                    <th>Form</th>
                    <th>Step</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($submissions as $submission)
                    <tr>
                        <td data-label="Tracking ID" class="mono">{{ $submission->tracking_id }}</td>
                        <td data-label="Employee">
                            <div><strong>{{ $submission->employee_name }}</strong></div>
                            <div style="color:#64748b;">{{ $submission->employee_email }}</div>
                        </td>
                        <td data-label="Department">{{ $submission->department }}</td>
                        <td data-label="Form">{{ $submission->form_type }}</td>
                        <td data-label="Step">
                            @php($flowTotal = is_array($submission->approval_flow_snapshot) ? count($submission->approval_flow_snapshot) : 0)
                            @if($flowTotal > 0)
                                {{ $submission->current_approval_step }}/{{ $flowTotal }}
                            @else
                                -
                            @endif
                        </td>
                        <td data-label="Status">
                            <span class="badge status-{{ $submission->status }}">{{ strtoupper($submission->status) }}</span>
                        </td>
                        <td data-label="Submitted">{{ optional($submission->submitted_at)->format('d M Y H:i') }}</td>
                        <td data-label="Action">
                            <div class="actions">
                                <form method="POST" action="{{ route('admin.submissions.status', $submission->tracking_id) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <button class="btn btn-success" type="submit">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.submissions.status', $submission->tracking_id) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="rejected">
                                    <button class="btn btn-danger" type="submit">Reject</button>
                                </form>
                                <form method="POST" action="{{ route('admin.submissions.status', $submission->tracking_id) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="in_review">
                                    <button class="btn btn-outline" type="submit">Review</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:20px;color:#64748b;">Belum ada submission.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:12px;">{{ $submissions->links() }}</div>
    </section>
@endsection
