<?php

namespace Teguh\FeatureSatuForm\Http\Controllers;

use Teguh\FeatureSatuForm\Models\FormTemplate;
use Teguh\FeatureSatuForm\Models\DependencyVerification;
use Teguh\FeatureSatuForm\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicFormController extends Controller
{
    public function index(): View
    {
        $forms = FormTemplate::where('is_published', 1)
            ->orderBy('department')
            ->orderBy('name')
            ->get();

        $forms->transform(function (FormTemplate $form): FormTemplate {
            $form->setAttribute('dependency_state', $this->dependencyState($form));
            return $form;
        });

        return view('feature-satu-form::public.forms-list', ['forms' => $forms]);
    }

    public function show(int $id): View|RedirectResponse
    {
        $form = FormTemplate::findOrFail($id);

        if (!$form->is_published) {
            abort(404, 'Form not published');
        }

        if (!session('public_auth', false)) {
            return redirect()->route('public.login', ['redirect_to' => route('public.forms.show', $id)]);
        }

        $dependencyState = $this->dependencyState($form);
        $dependencyVerification = $this->dependencyVerification($form);
        $dependencyVerified = $this->isDependencyVerified($form);

        $guestContext = $this->guestContext();

        return view('feature-satu-form::public.form-fill', [
            'form' => $form,
            'guestContext' => $guestContext,
            'dependencyState' => $dependencyState,
            'dependencyVerification' => $dependencyVerification,
            'dependencyVerified' => $dependencyVerified,
        ]);
    }

    public function verifyDependency(Request $request, int $id): RedirectResponse
    {
        $form = FormTemplate::findOrFail($id);

        if (!$form->is_published) {
            abort(404, 'Form not published');
        }

        if (!session('public_auth', false)) {
            return redirect()->route('public.login', ['redirect_to' => route('public.forms.show', $id)]);
        }

        $dependencyState = $this->dependencyState($form);
        if (!$dependencyState['has_dependency']) {
            return redirect()->route('public.forms.show', $id)->with('error', 'Form ini tidak memiliki dependency.');
        }

        if (empty($dependencyState['dependency_form'])) {
            return redirect()->route('public.forms.show', $id)->with('error', (string) ($dependencyState['message'] ?? 'Dependency form tidak ditemukan.'));
        }

        $validated = $request->validate([
            'dependency_tracking_id' => ['required', 'string', 'max:50'],
        ]);

        $trackingId = strtoupper(trim((string) $validated['dependency_tracking_id']));
        $dependencyForm = $dependencyState['dependency_form'];

        if (DependencyVerification::query()->where('tracking_id', $trackingId)->exists()) {
            return back()
                ->withInput()
                ->withErrors([
                    'dependency_tracking_id' => 'Nomor track sudah dipakai untuk verifikasi dependency dan tidak bisa digunakan lagi.',
                ]);
        }

        $submission = Submission::query()
            ->where('tracking_id', $trackingId)
            ->where('template_id', $dependencyForm->id)
            ->where('status', 'approved')
            ->first();

        if (!$submission) {
            return back()
                ->withInput()
                ->withErrors([
                    'dependency_tracking_id' => 'Nomor track tidak valid atau dependency belum approved.',
                ]);
        }

        $this->storeDependencyVerification($form, [
            'tracking_id' => $trackingId,
            'submission_id' => $submission->id,
            'template_id' => $dependencyForm->id,
            'form_code' => $dependencyForm->form_code,
            'verified_at' => now()->toIso8601String(),
        ]);

        return redirect()
            ->route('public.forms.show', $id)
            ->with('success', 'Dependency terverifikasi. Sekarang form bisa diisi.');
    }

    public function store(Request $request, int $id): View|RedirectResponse
    {
        $form = FormTemplate::findOrFail($id);

        if (!$form->is_published) {
            abort(404, 'Form not published');
        }

        if (!session('public_auth', false)) {
            return redirect()->route('public.login', ['redirect_to' => route('public.forms.show', $id)]);
        }

        $dependencyState = $this->dependencyState($form);
        if ($dependencyState['has_dependency'] && !$this->isDependencyVerified($form)) {
            return back()->with('error', 'Verifikasi dependency dulu sebelum mengisi form.');
        }

        $guestContext = $this->guestContext();

        if ($guestContext !== null) {
            $validated = $guestContext;
        } else {
            $validated = $request->validate([
                'employeeName' => 'required|string|max:255',
                'employeeEmail' => 'required|email',
                'department' => 'required|string|max:100',
            ]);
        }

        $fieldsConfig = is_array($form->fields_config) ? $form->fields_config : [];
        $formData = [];
        foreach ($fieldsConfig as $field) {
            if (!is_array($field)) {
                continue;
            }

            $fieldId = (string) ($field['id'] ?? '');
            if ($fieldId === '') {
                continue;
            }

            $fieldType = (string) ($field['type'] ?? 'text');
            $isRequired = (bool) ($field['required'] ?? false);

            if ($fieldType === 'checkbox') {
                $value = $request->input($fieldId, []);
                $formData[$fieldId] = is_array($value) ? $value : [];
                if ($isRequired && count($formData[$fieldId]) === 0) {
                    return back()->withInput()->with('error', 'Field wajib belum diisi: ' . ($field['label'] ?? $fieldId));
                }
                continue;
            }

            if ($fieldType === 'file') {
                $file = $request->file($fieldId);
                if ($isRequired && !$file) {
                    return back()->withInput()->with('error', 'Field wajib belum diisi: ' . ($field['label'] ?? $fieldId));
                }

                if ($file) {
                    $path = $file->store('submissions', 'public');
                    $formData[$fieldId] = $path;
                } else {
                    $formData[$fieldId] = null;
                }
                continue;
            }

            $value = $request->input($fieldId, '');
            if (is_array($value)) {
                $value = json_encode($value);
            }

            if ($isRequired && trim((string) $value) === '') {
                return back()->withInput()->with('error', 'Field wajib belum diisi: ' . ($field['label'] ?? $fieldId));
            }

            $formData[$fieldId] = $value;
        }

        $trackingId = $this->generateTrackingId();
        $approvalFlow = $this->normalizeApprovalFlow($form->approval_flow_config ?? []);

        $history = [
            [
                'timestamp' => now()->toIso8601String(),
                'actor' => 'system',
                'action' => 'submitted',
                'status' => 'in_review',
                'note' => 'Form submitted by requestor.',
            ],
        ];

        $submission = Submission::create([
            'tracking_id' => $trackingId,
            'template_id' => $form->id,
            'form_type' => $form->name,
            'department' => $form->department,
            'employee_name' => $validated['employeeName'],
            'employee_email' => $validated['employeeEmail'],
            'notes' => $validated['department'],
            'form_data' => $formData,
            'status' => 'in_review',
            'current_approval_step' => 1,
            'approval_flow_snapshot' => $approvalFlow,
            'approval_history' => $history,
            'submitted_at' => now(),
        ]);

        return view('feature-satu-form::public.form-success', ['submission' => $submission]);
    }

    public function track(Request $request): View
    {
        $submission = null;
        $trackingId = strtoupper(trim((string) $request->query('tracking_id', '')));

        if ($trackingId !== '') {
            $submission = Submission::query()->where('tracking_id', $trackingId)->first();
        }

        return view('feature-satu-form::public.track', [
            'trackingId' => $trackingId,
            'submission' => $submission,
        ]);
    }

    private function guestContext(): ?array
    {
        if (session('public_level') !== 'guest') {
            return null;
        }

        return [
            'employeeName' => (string) session('public_name', 'guest'),
            'employeeEmail' => (string) session('public_email', 'guest@example.com'),
            'department' => (string) session('public_department', 'HR'),
        ];
    }

    private function dependencyState(FormTemplate $form): array
    {
        $dependencyCode = trim((string) ($form->dependency_form_code ?? ''));

        if ($dependencyCode === '') {
            return [
                'has_dependency' => false,
                'is_blocked' => false,
                'message' => null,
                'dependency_form' => null,
            ];
        }

        $dependencyForm = FormTemplate::query()->where('form_code', $dependencyCode)->first();
        if (!$dependencyForm) {
            return [
                'has_dependency' => true,
                'is_blocked' => true,
                'message' => 'Dependency form ' . $dependencyCode . ' tidak ditemukan.',
                'dependency_form' => null,
            ];
        }

        $isApproved = Submission::query()
            ->where('template_id', $dependencyForm->id)
            ->where('status', 'approved')
            ->exists();

        return [
            'has_dependency' => true,
            'is_blocked' => !$isApproved,
            'message' => $isApproved
                ? null
                : 'Menunggu form ' . $dependencyCode . ' selesai approved.',
            'dependency_form' => $dependencyForm,
        ];
    }

    private function dependencyVerificationKey(FormTemplate $form): string
    {
        return 'public_dependency_verifications.' . $form->id;
    }

    private function dependencyVerification(FormTemplate $form): ?array
    {
        $verification = session($this->dependencyVerificationKey($form));

        return is_array($verification) ? $verification : null;
    }

    private function isDependencyVerified(FormTemplate $form): bool
    {
        $state = $this->dependencyState($form);

        if (empty($state['has_dependency']) || empty($state['dependency_form'])) {
            return true;
        }

        $verification = $this->dependencyVerification($form);
        if (!is_array($verification)) {
            return false;
        }

        if (strcasecmp((string) ($verification['form_code'] ?? ''), (string) $state['dependency_form']->form_code) !== 0) {
            return false;
        }

        $trackingId = strtoupper(trim((string) ($verification['tracking_id'] ?? '')));
        if ($trackingId === '') {
            return false;
        }

        return Submission::query()
            ->where('tracking_id', $trackingId)
            ->where('template_id', $state['dependency_form']->id)
            ->where('status', 'approved')
            ->exists();
    }

    private function storeDependencyVerification(FormTemplate $form, array $payload): void
    {
        DependencyVerification::query()->create([
            'tracking_id' => (string) $payload['tracking_id'],
            'form_template_id' => $form->id,
            'dependency_form_template_id' => (int) $payload['template_id'],
            'submission_id' => $payload['submission_id'] ?? null,
            'form_code' => (string) $payload['form_code'],
            'verified_at' => now(),
        ]);

        session()->put($this->dependencyVerificationKey($form), $payload);
    }

    private function generateTrackingId(): string
    {
        do {
            $trackingId = 'TRK-' . strtoupper(Str::random(10));
        } while (Submission::query()->where('tracking_id', $trackingId)->exists());

        return $trackingId;
    }

    private function normalizeApprovalFlow(array $raw): array
    {
        $result = [];

        foreach ($raw as $index => $step) {
            if (!is_array($step)) {
                continue;
            }

            $name = trim((string) ($step['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $result[] = [
                'order' => count($result) + 1,
                'name' => $name,
                'role' => trim((string) ($step['role'] ?? 'Approver')),
                'level' => strtolower((string) ($step['level'] ?? 'supervisor')),
                'status' => 'pending',
            ];
        }

        if (count($result) === 0) {
            $result[] = [
                'order' => 1,
                'name' => 'Supervisor Approval',
                'role' => 'Approver',
                'level' => 'supervisor',
                'status' => 'pending',
            ];
        }

        return $result;
    }
}
