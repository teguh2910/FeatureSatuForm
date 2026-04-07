<?php

namespace Teguh\FeatureSatuForm\Http\Controllers\Api;

use Teguh\FeatureSatuForm\Http\Controllers\Controller;
use Teguh\FeatureSatuForm\Models\FormTemplate;
use Teguh\FeatureSatuForm\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubmissionController extends Controller
{
    public function templates(): JsonResponse
    {
        $templates = FormTemplate::query()
            ->where('is_published', true)
            ->orderBy('name')
            ->get()
            ->map(function (FormTemplate $template): array {
                return [
                    'id' => $template->id,
                    'name' => $template->name,
                    'description' => $template->description,
                    'department' => $template->department,
                    'fieldsConfig' => $template->fields_config ?? [],
                ];
            });

        return response()->json([
            'data' => $templates,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employeeName' => ['required', 'string', 'max:255'],
            'employeeEmail' => ['required', 'email', 'max:255'],
            'department' => ['required', 'string', 'max:10'],
            'templateId' => ['required', 'integer', 'exists:form_templates,id'],
            'notes' => ['nullable', 'string'],
            'formData' => ['nullable', 'array'],
        ]);

        $template = FormTemplate::query()->find((int) $validated['templateId']);

        if (!$template || !$template->is_published) {
            return response()->json([
                'message' => 'Template tidak tersedia atau belum dipublish.',
            ], 422);
        }

        $trackingId = $this->generateTrackingId();

        $submission = Submission::create([
            'tracking_id' => $trackingId,
            'template_id' => $template->id,
            'employee_name' => $validated['employeeName'],
            'employee_email' => $validated['employeeEmail'],
            'department' => $validated['department'],
            'form_type' => $template->name,
            'notes' => $validated['notes'] ?? null,
            'form_data' => $validated['formData'] ?? [],
            'status' => 'in_review',
            'submitted_at' => now(),
        ]);

        return response()->json([
            'message' => 'Submission created',
            'data' => [
                'id' => $submission->tracking_id,
                'employeeName' => $submission->employee_name,
                'employeeEmail' => $submission->employee_email,
                'department' => $submission->department,
                'templateId' => $submission->template_id,
                'formType' => $submission->form_type,
                'notes' => $submission->notes,
                'formData' => $submission->form_data ?? [],
                'status' => $submission->status,
                'submittedAt' => $submission->submitted_at?->toIso8601String(),
            ],
        ], 201);
    }

    public function show(string $trackingId): JsonResponse
    {
        $submission = Submission::query()
            ->where('tracking_id', strtoupper($trackingId))
            ->first();

        if (!$submission) {
            return response()->json([
                'message' => 'Submission not found',
            ], 404);
        }

        return response()->json([
            'data' => [
                'id' => $submission->tracking_id,
                'employeeName' => $submission->employee_name,
                'employeeEmail' => $submission->employee_email,
                'department' => $submission->department,
                'templateId' => $submission->template_id,
                'formType' => $submission->form_type,
                'notes' => $submission->notes,
                'formData' => $submission->form_data ?? [],
                'status' => $submission->status,
                'submittedAt' => $submission->submitted_at?->toIso8601String(),
            ],
        ]);
    }

    private function generateTrackingId(): string
    {
        do {
            $candidate = 'SUB-' . strtoupper(Str::random(8));
        } while (Submission::query()->where('tracking_id', $candidate)->exists());

        return $candidate;
    }
}
