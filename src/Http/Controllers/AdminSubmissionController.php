<?php

namespace Teguh\FeatureSatuForm\Http\Controllers;

use Teguh\FeatureSatuForm\Models\FormTemplate;
use Teguh\FeatureSatuForm\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSubmissionController extends Controller
{
    public function index(Request $request): View
    {
        $userDept = session('admin_department');

        $query = Submission::query()->orderByDesc('submitted_at');

        if (!empty($userDept)) {
            $allowedFormIds = FormTemplate::query()
                ->where('department', $userDept)
                ->pluck('id');
            $query->whereIn('template_id', $allowedFormIds);
        }

        if ($request->filled('q')) {
            $term = trim((string) $request->query('q'));
            $query->where(function ($builder) use ($term): void {
                $builder
                    ->where('tracking_id', 'like', '%' . $term . '%')
                    ->orWhere('employee_name', 'like', '%' . $term . '%')
                    ->orWhere('employee_email', 'like', '%' . $term . '%')
                    ->orWhere('form_type', 'like', '%' . $term . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', (string) $request->query('status'));
        }

        if ($request->filled('department') && empty($userDept)) {
            $query->where('department', (string) $request->query('department'));
        }

        $submissions = $query->paginate(15)->withQueryString();

        return view('feature-satu-form::admin.submissions', [
            'submissions' => $submissions,
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'status' => (string) $request->query('status', ''),
                'department' => !empty($userDept) ? (string) $userDept : (string) $request->query('department', ''),
            ],
            'userDept' => $userDept,
        ]);
    }

    public function updateStatus(Request $request, string $trackingId): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:in_review,approved,rejected'],
        ]);

        $submission = Submission::query()->where('tracking_id', strtoupper($trackingId))->first();

        if (!$submission) {
            return back()->with('error', 'Submission tidak ditemukan.');
        }

        $userDept = session('admin_department');
        if (!empty($userDept)) {
            $form = FormTemplate::query()->find($submission->template_id);
            if (!$form || $form->department !== $userDept) {
                abort(403, 'Anda tidak bisa mengubah submission departemen lain.');
            }
        }

        $flow = is_array($submission->approval_flow_snapshot) ? $submission->approval_flow_snapshot : [];
        $history = is_array($submission->approval_history) ? $submission->approval_history : [];
        $currentStep = max(1, (int) $submission->current_approval_step);
        $currentApproverLevel = strtolower((string) session('admin_level', ''));
        $isSuperAdmin = (string) session('admin_username', '') === (string) env('SUPER_ADMIN_USERNAME', 'admin');

        if (count($flow) > 0) {
            $currentIndex = $currentStep - 1;
            $requiredLevel = strtolower((string) ($flow[$currentIndex]['level'] ?? 'supervisor'));
            if (!$isSuperAdmin && $validated['status'] !== 'in_review' && $currentApproverLevel !== $requiredLevel) {
                return back()->with('error', 'Approval tahap ini hanya untuk level: ' . strtoupper($requiredLevel));
            }
        }

        if ($validated['status'] === 'approved') {
            if (count($flow) > 0) {
                $currentIndex = $currentStep - 1;
                if (isset($flow[$currentIndex])) {
                    $flow[$currentIndex]['status'] = 'approved';
                }

                $hasNextStep = isset($flow[$currentStep]);
                if ($hasNextStep) {
                    $submission->status = 'in_review';
                    $submission->current_approval_step = $currentStep + 1;
                } else {
                    $submission->status = 'approved';
                    $submission->current_approval_step = $currentStep;
                }
            } else {
                $submission->status = 'approved';
            }
        } elseif ($validated['status'] === 'rejected') {
            if (count($flow) > 0) {
                $currentIndex = $currentStep - 1;
                if (isset($flow[$currentIndex])) {
                    $flow[$currentIndex]['status'] = 'rejected';
                }
            }
            $submission->status = 'rejected';
        } else {
            $historyStep = $currentStep;

            if (count($flow) > 0) {
                $reviewIndex = null;

                for ($index = count($flow) - 1; $index >= 0; $index--) {
                    $stepStatus = strtolower((string) ($flow[$index]['status'] ?? 'pending'));
                    if ($stepStatus !== 'pending') {
                        $reviewIndex = $index;
                        break;
                    }
                }

                if ($reviewIndex !== null) {
                    for ($index = $reviewIndex; $index < count($flow); $index++) {
                        $flow[$index]['status'] = 'pending';
                    }

                    $submission->current_approval_step = $reviewIndex + 1;
                    $historyStep = $reviewIndex + 1;
                }
            }

            $submission->status = 'in_review';
        }

        $history[] = [
            'timestamp' => now()->toIso8601String(),
            'actor' => (string) session('admin_name', 'admin'),
            'action' => $validated['status'] === 'in_review' ? 'review' : $validated['status'],
            'status' => $submission->status,
            'step' => $historyStep ?? $currentStep,
            'level' => $currentApproverLevel,
        ];

        $submission->approval_flow_snapshot = $flow;
        $submission->approval_history = $history;
        $submission->save();

        return back()->with('success', 'Status berhasil diperbarui ke ' . $validated['status'] . '.');
    }
}
