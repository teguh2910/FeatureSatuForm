<?php

namespace Teguh\FeatureSatuForm\Http\Controllers;

use Teguh\FeatureSatuForm\Models\FormTemplate;
use Teguh\FeatureSatuForm\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminFormBuilderController extends Controller
{
    private const APPROVAL_LEVELS = ['supervisor', 'manager', 'gm', 'director'];

    private const ALLOWED_FIELD_TYPES = [
        'text',
        'textarea',
        'number',
        'email',
        'date',
        'dropdown',
        'radio',
        'checkbox',
        'file',
        'calculation',
        'table',
    ];

    public function index(): View
    {
        $userDept = session('admin_department');

        $query = FormTemplate::query();
        if (!empty($userDept)) {
            $query->where('department', $userDept);
        }

        $forms = $query->orderByDesc('created_at')->paginate(12);

        $forms->getCollection()->transform(function (FormTemplate $form): FormTemplate {
            $form->setAttribute('dependency_state', $this->dependencyState($form));
            return $form;
        });

        return view('feature-satu-form::admin.forms', [
            'forms' => $forms,
            'userDept' => $userDept,
            'dependencyForms' => $this->dependencyCandidates(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $userDept = session('admin_department');

        $validated = $request->validate([
            'form_code' => ['required', 'string', 'max:50', 'unique:form_templates,form_code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'department' => ['required', 'in:HR,FIN,IT,OPS'],
            'dependency_form_code' => ['nullable', 'string', 'max:50'],
        ]);

        $department = !empty($userDept) ? (string) $userDept : $validated['department'];

        $dependencyFormCode = trim((string) ($validated['dependency_form_code'] ?? ''));
        if ($dependencyFormCode !== '' && strcasecmp($dependencyFormCode, $validated['form_code']) === 0) {
            return back()->withInput()->with('error', 'Form tidak boleh bergantung pada form yang sama.');
        }

        if ($dependencyFormCode !== '' && !in_array($dependencyFormCode, $this->dependencyCodes(), true)) {
            return back()->withInput()->with('error', 'Dependency form harus berupa form yang sudah dipublish.');
        }

        FormTemplate::query()->create([
            'form_code' => $validated['form_code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'fields_config' => [],
            'approval_flow_config' => [
                ['order' => 1, 'name' => 'Supervisor Approval', 'role' => 'Approver', 'level' => 'supervisor'],
            ],
            'dependency_form_code' => $dependencyFormCode !== '' ? $dependencyFormCode : null,
            'department' => $department,
            'is_published' => false,
        ]);

        return back()->with('success', 'Form berhasil dibuat.');
    }

    public function edit(FormTemplate $formTemplate): View
    {
        if (!$this->canAccessForm($formTemplate)) {
            abort(403, 'Anda tidak bisa mengakses form departemen lain.');
        }

        return view('feature-satu-form::admin.form-edit', [
            'form' => $formTemplate,
            'dependencyForms' => $this->dependencyCandidates($formTemplate),
        ]);
    }

    public function update(Request $request, FormTemplate $formTemplate): RedirectResponse
    {
        if (!$this->canAccessForm($formTemplate)) {
            abort(403, 'Anda tidak bisa mengubah form departemen lain.');
        }

        $userDept = session('admin_department');

        $validated = $request->validate([
            'form_code' => ['required', 'string', 'max:50', Rule::unique('form_templates', 'form_code')->ignore($formTemplate->id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'department' => ['required', 'in:HR,FIN,IT,OPS'],
            'fields_config_json' => ['required', 'string'],
            'approval_steps_text' => ['nullable', 'string'],
            'dependency_form_code' => ['nullable', 'string', 'max:50'],
        ]);

        $department = !empty($userDept) ? (string) $userDept : $validated['department'];
        $dependencyFormCode = trim((string) ($validated['dependency_form_code'] ?? ''));

        if ($dependencyFormCode !== '' && strcasecmp($dependencyFormCode, $validated['form_code']) === 0) {
            return back()->withInput()->with('error', 'Form tidak boleh bergantung pada form yang sama.');
        }

        if ($dependencyFormCode !== '' && !in_array($dependencyFormCode, $this->dependencyCodes($formTemplate), true)) {
            return back()->withInput()->with('error', 'Dependency form harus berupa form yang sudah dipublish.');
        }

        $formTemplate->update([
            'form_code' => $validated['form_code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'department' => $department,
            'fields_config' => $this->normalizeFieldsConfig($validated['fields_config_json']),
            'approval_flow_config' => $this->normalizeApprovalFlow((string) ($validated['approval_steps_text'] ?? '')),
            'dependency_form_code' => $dependencyFormCode !== '' ? $dependencyFormCode : null,
        ]);

        return redirect()->route('admin.forms.index')->with('success', 'Form berhasil diperbarui.');
    }

    public function togglePublish(FormTemplate $formTemplate): RedirectResponse
    {
        if (!$this->canAccessForm($formTemplate)) {
            abort(403, 'Anda tidak bisa mengubah status form departemen lain.');
        }

        $formTemplate->is_published = !$formTemplate->is_published;
        $formTemplate->save();

        return back()->with('success', $formTemplate->is_published ? 'Form dipublish.' : 'Form di-unpublish.');
    }

    public function destroy(FormTemplate $formTemplate): RedirectResponse
    {
        if (!$this->canAccessForm($formTemplate)) {
            abort(403, 'Anda tidak bisa menghapus form departemen lain.');
        }

        $formTemplate->delete();

        return back()->with('success', 'Form berhasil dihapus.');
    }

    private function normalizeFieldsConfig(string $rawJson): array
    {
        $decoded = json_decode($rawJson, true);

        if (!is_array($decoded)) {
            return [];
        }

        $result = [];

        foreach ($decoded as $item) {
            if (!is_array($item)) {
                continue;
            }

            $type = (string) ($item['type'] ?? '');
            if (!in_array($type, self::ALLOWED_FIELD_TYPES, true)) {
                continue;
            }

            $field = [
                'id' => (string) ($item['id'] ?? uniqid('fld_', true)),
                'type' => $type,
                'label' => trim((string) ($item['label'] ?? '')),
                'required' => (bool) ($item['required'] ?? false),
            ];

            if (in_array($type, ['dropdown', 'radio', 'checkbox'], true)) {
                $field['options'] = array_values(array_filter(array_map('trim', (array) ($item['options'] ?? [])), fn ($v) => $v !== ''));
            }

            if ($type === 'calculation') {
                $field['formula'] = trim((string) ($item['formula'] ?? ''));
            }

            if ($type === 'table') {
                $field['tableColumns'] = $this->normalizeTableColumns((array) ($item['tableColumns'] ?? []));
            }

            $result[] = $field;
        }

        return $result;
    }

    private function normalizeTableColumns(array $columns): array
    {
        $result = [];

        foreach ($columns as $column) {
            if (!is_array($column)) {
                continue;
            }

            $name = trim((string) ($column['name'] ?? ''));
            $type = (string) ($column['type'] ?? 'text');
            if ($name === '' || !in_array($type, ['text', 'number', 'dropdown', 'calc'], true)) {
                continue;
            }

            $entry = [
                'id' => (string) ($column['id'] ?? uniqid('col_', true)),
                'name' => $name,
                'type' => $type,
            ];

            if ($type === 'dropdown') {
                $entry['options'] = array_values(array_filter(array_map('trim', (array) ($column['options'] ?? [])), fn ($v) => $v !== ''));
            }

            if ($type === 'calc') {
                $entry['formula'] = trim((string) ($column['formula'] ?? ''));
            }

            $result[] = $entry;
        }

        return $result;
    }

    private function canAccessForm(FormTemplate $formTemplate): bool
    {
        $userDept = session('admin_department');

        return empty($userDept) || $formTemplate->department === $userDept;
    }

    private function dependencyCandidates(?FormTemplate $currentForm = null)
    {
        $publishedForms = FormTemplate::query()
            ->where('is_published', true)
            ->orderBy('form_code')
            ->get(['id', 'form_code', 'name', 'is_published']);

        if ($currentForm === null) {
            return $publishedForms;
        }

        $currentDependencyCode = trim((string) ($currentForm->dependency_form_code ?? ''));
        if ($currentDependencyCode === '') {
            return $publishedForms->filter(fn (FormTemplate $form) => strcasecmp($form->form_code, $currentForm->form_code) !== 0)->values();
        }

        $currentDependency = FormTemplate::query()
            ->where('form_code', $currentDependencyCode)
            ->first(['id', 'form_code', 'name', 'is_published']);

        if ($currentDependency === null) {
            return $publishedForms->filter(fn (FormTemplate $form) => strcasecmp($form->form_code, $currentForm->form_code) !== 0)->values();
        }

        $merged = $publishedForms->filter(function (FormTemplate $form) use ($currentForm): bool {
            return strcasecmp($form->form_code, $currentForm->form_code) !== 0;
        });

        if ($merged->contains(fn (FormTemplate $form) => strcasecmp($form->form_code, $currentDependency->form_code) === 0)) {
            return $merged->values();
        }

        return $merged->prepend($currentDependency)->values();
    }

    private function dependencyCodes(?FormTemplate $currentForm = null): array
    {
        return $this->dependencyCandidates($currentForm)
            ->pluck('form_code')
            ->map(fn ($code) => (string) $code)
            ->all();
    }

    private function dependencyState(FormTemplate $formTemplate): array
    {
        $dependencyCode = trim((string) ($formTemplate->dependency_form_code ?? ''));

        if ($dependencyCode === '') {
            return [
                'is_blocked' => false,
                'message' => null,
                'dependency_code' => null,
            ];
        }

        $dependencyForm = FormTemplate::query()->where('form_code', $dependencyCode)->first();
        if (!$dependencyForm) {
            return [
                'is_blocked' => true,
                'message' => 'Dependency form ' . $dependencyCode . ' tidak ditemukan.',
                'dependency_code' => $dependencyCode,
            ];
        }

        $isApproved = Submission::query()
            ->where('template_id', $dependencyForm->id)
            ->where('status', 'approved')
            ->exists();

        return [
            'is_blocked' => !$isApproved,
            'message' => $isApproved
                ? null
                : 'Menunggu form ' . $dependencyCode . ' selesai approved.',
            'dependency_code' => $dependencyCode,
        ];
    }

    private function normalizeApprovalFlow(string $rawText): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $rawText);
        if (!is_array($lines)) {
            return [];
        }

        $result = [];
        foreach ($lines as $line) {
            $parts = array_map('trim', explode('|', $line));
            $stepName = $parts[0] ?? '';
            if ($stepName === '') {
                continue;
            }

            $stepLevel = strtolower((string) ($parts[1] ?? ''));
            if (!in_array($stepLevel, self::APPROVAL_LEVELS, true)) {
                $stepLevel = 'supervisor';
            }

            $result[] = [
                'order' => count($result) + 1,
                'name' => $stepName,
                'role' => 'Approver',
                'level' => $stepLevel,
            ];
        }

        if (count($result) === 0) {
            $result[] = [
                'order' => 1,
                'name' => 'Supervisor Approval',
                'role' => 'Approver',
                'level' => 'supervisor',
            ];
        }

        return $result;
    }
}
