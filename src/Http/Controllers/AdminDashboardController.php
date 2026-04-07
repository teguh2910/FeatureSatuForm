<?php

namespace Teguh\FeatureSatuForm\Http\Controllers;

use Teguh\FeatureSatuForm\Models\FormTemplate;
use Teguh\FeatureSatuForm\Models\Submission;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $userDept = session('admin_department');

        $formsQuery = FormTemplate::query();
        if (!empty($userDept)) {
            $formsQuery->where('department', $userDept);
        }

        $allowedFormIds = (clone $formsQuery)->pluck('id');

        $submissionsQuery = Submission::query();
        if (!empty($userDept)) {
            $submissionsQuery->whereIn('template_id', $allowedFormIds);
        }

        $stats = [
            'totalForms' => $formsQuery->count(),
            'totalSubmissions' => (clone $submissionsQuery)->count(),
            'pendingSubmissions' => (clone $submissionsQuery)->whereIn('status', ['in_review'])->count(),
            'approvedSubmissions' => (clone $submissionsQuery)->where('status', 'approved')->count(),
        ];

        $recentSubmissions = $submissionsQuery
            ->orderByDesc('submitted_at')
            ->limit(8)
            ->get();

        return view('feature-satu-form::admin.dashboard', [
            'stats' => $stats,
            'recentSubmissions' => $recentSubmissions,
        ]);
    }
}
