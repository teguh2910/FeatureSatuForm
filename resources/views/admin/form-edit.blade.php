@extends('feature-satu-form::admin.layout')

@section('title', 'Edit Form')
@section('active_nav', 'forms')

@section('page_styles')
    <style>
        .form-meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        @media (max-width: 768px) {
            .form-meta-grid { grid-template-columns: 1fr; }
        }
        .approval-step-card {
            border: 1px solid var(--fs-border);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 8px;
            background: #f8f9fc;
            display: grid;
            grid-template-columns: 1fr 180px auto;
            gap: 12px;
            align-items: end;
        }
        @media (max-width: 768px) {
            .approval-step-card { grid-template-columns: 1fr; }
        }
        .field-card {
            border: 1px solid var(--fs-border);
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 12px;
            background: #fafbff;
        }
        .field-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--fs-border);
        }
        @media (max-width: 768px) {
            .approval-tools-grid { grid-template-columns: 1fr !important; }
        }
        .approval-tools-grid {
            display: grid;
            grid-template-columns: 2fr 1fr auto;
            gap: 8px;
            margin-bottom: 12px;
        }
    </style>
@endsection

@section('content')
    <div class="card border-0 rounded-3">
        <div class="card-header bg-white border-0 rounded-3 py-3">
            <h5 class="mb-0 text-primary"><i class="ri-file-edit-line me-2"></i>Edit Form</h5>
        </div>
        <div class="card-body">
            <form id="form-edit-form" method="POST" action="{{ route('admin.forms.update', $form) }}">
                @csrf
                @method('PUT')

                <!-- Basic Info -->
                <div class="mb-4">
                    <label class="form-label small fw-semibold text-muted text-uppercase" for="name">Form Name</label>
                    <input id="name" name="name" value="{{ old('name', $form->name) }}" class="form-control" required>
                </div>

                <div class="form-meta-grid mb-4">
                    <div>
                        <label class="form-label small fw-semibold text-muted text-uppercase" for="form_code">Form Code</label>
                        <input id="form_code" name="form_code" value="{{ old('form_code', $form->form_code) }}" class="form-control" required>
                    </div>
                    <div>
                        <label class="form-label small fw-semibold text-muted text-uppercase" for="dependency_form_code">Dependency Form</label>
                        <select id="dependency_form_code" name="dependency_form_code" class="form-select">
                            <option value="">No dependency</option>
                            @foreach(($dependencyForms ?? []) as $dependencyForm)
                                <option value="{{ $dependencyForm->form_code }}" @selected(old('dependency_form_code', $form->dependency_form_code) === $dependencyForm->form_code)>
                                    {{ $dependencyForm->form_code }} - {{ $dependencyForm->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-meta-grid mb-4">
                    <div>
                        <label class="form-label small fw-semibold text-muted text-uppercase" for="department">Department</label>
                        <select id="department" name="department" class="form-select" required>
                            <option value="HR" @selected(old('department', $form->department) === 'HR')>HR</option>
                            <option value="FIN" @selected(old('department', $form->department) === 'FIN')>FIN</option>
                            <option value="IT" @selected(old('department', $form->department) === 'IT')>IT</option>
                            <option value="OPS" @selected(old('department', $form->department) === 'OPS')>OPS</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label small fw-semibold text-muted text-uppercase" for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="2">{{ old('description', $form->description) }}</textarea>
                    </div>
                </div>

                <p class="small text-muted mb-4">
                    <i class="ri-information-line me-1"></i>
                    Dependency form hanya bisa aktif jika prerequisite form sudah approved.
                </p>

                <hr class="my-4">

                <!-- Approval Flow Steps -->
                <h6 class="text-uppercase text-muted fw-bold mb-3">
                    <i class="ri-git-branch-line me-1"></i> Approval Flow Steps
                </h6>

                <div class="approval-tools-grid mb-2">
                    <input type="text" id="new-approval-step-name" class="form-control" placeholder="Step name, ex: Supervisor Approval">
                    <select id="new-approval-step-level" class="form-select">
                        <option value="supervisor">Supervisor</option>
                        <option value="manager">Manager</option>
                        <option value="gm">GM</option>
                        <option value="director">Director</option>
                    </select>
                    <button type="button" class="btn btn-primary" id="add-approval-step-btn">
                        <i class="ri-add-line me-1"></i> Add Step
                    </button>
                </div>

                <div id="approval-steps-list" class="mb-3"></div>
                <textarea id="approval_steps_text" name="approval_steps_text" class="d-none">{{ collect(old('approval_steps_text')
                    ? preg_split('/\r\n|\r|\n/', (string) old('approval_steps_text'))
                    : collect($form->approval_flow_config ?? [])->map(function ($step) {
                        $name = trim((string) ($step['name'] ?? ''));
                        $level = strtolower((string) ($step['level'] ?? 'supervisor'));
                        return $name !== '' ? ($name . '|' . $level) : null;
                    })->filter()->all()
                )->filter()->implode("\n") }}</textarea>
                <p class="small text-muted mb-4">
                    Setiap tahap memilih level approver. Hanya level yang sesuai bisa approve pada tahap tersebut.
                </p>

                <hr class="my-4">

                <!-- Built-in Fields -->
                <h6 class="text-uppercase text-muted fw-bold mb-3">
                    <i class="ri-list-check-2 me-1"></i> Built-in Fields
                </h6>

                <div class="d-flex gap-3 mb-3 flex-wrap" style="max-width: 600px;">
                    <select id="new-field-type" class="form-select" style="max-width: 200px;">
                        <option value="text">Text</option>
                        <option value="textarea">Text Area</option>
                        <option value="number">Number</option>
                        <option value="email">Email</option>
                        <option value="date">Date</option>
                        <option value="dropdown">Dropdown</option>
                        <option value="radio">Radio</option>
                        <option value="checkbox">Checkbox</option>
                        <option value="file">File Upload</option>
                        <option value="calculation">Calculation</option>
                        <option value="table">Table</option>
                    </select>
                    <button type="button" class="btn btn-primary" id="add-field-btn">
                        <i class="ri-add-line me-1"></i> Add Field
                    </button>
                </div>

                <input type="hidden" id="fields_config_json" name="fields_config_json" value="{{ old('fields_config_json', json_encode($form->fields_config ?? [])) }}">

                <div id="field-list" class="mb-4"></div>

                <p class="small text-muted mb-4">
                    <i class="ri-file-text-line me-1"></i>
                    Table columns format per baris: name|type|extra. Contoh: Qty|number atau Total|calc|{Qty} * {Price}
                </p>

                <!-- Actions -->
                <div class="d-flex gap-2 justify-content-end border-top pt-4">
                    <a href="{{ route('admin.forms.index') }}" class="btn btn-outline-secondary">
                        <i class="ri-arrow-left-line me-1"></i> Cancel
                    </a>
                    <button class="btn btn-primary" type="submit">
                        <i class="ri-save-line me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const fieldListElement = document.getElementById('field-list');
    const addFieldButton = document.getElementById('add-field-btn');
    const fieldTypeSelect = document.getElementById('new-field-type');
    const fieldsInput = document.getElementById('fields_config_json');
    const formElement = document.getElementById('form-edit-form');
    const approvalStepsListElement = document.getElementById('approval-steps-list');
    const newApprovalStepNameInput = document.getElementById('new-approval-step-name');
    const newApprovalStepLevelSelect = document.getElementById('new-approval-step-level');
    const addApprovalStepButton = document.getElementById('add-approval-step-btn');
    const approvalStepsTextInput = document.getElementById('approval_steps_text');

    let fields = @json(old('fields_config_json') ? json_decode(old('fields_config_json'), true) : ($form->fields_config ?? []));
    if (!Array.isArray(fields)) fields = [];

    function parseApprovalSteps(rawText) {
        return String(rawText || '')
            .split('\n').map((line) => line.trim()).filter((line) => line.length > 0)
            .map((line) => {
                const [nameRaw = '', levelRaw = 'supervisor'] = line.split('|');
                const name = nameRaw.trim();
                const level = String(levelRaw).trim().toLowerCase();
                if (!name) return null;
                return { name, level: ['supervisor', 'manager', 'gm', 'director'].includes(level) ? level : 'supervisor' };
            }).filter(Boolean);
    }

    let approvalSteps = parseApprovalSteps(approvalStepsTextInput ? approvalStepsTextInput.value : '');
    if (fieldsInput) fieldsInput.value = JSON.stringify(fields);

    function uid(prefix = 'fld') {
        return `${prefix}_${Date.now().toString(36)}_${Math.random().toString(36).slice(2, 8)}`;
    }

    function toApprovalStepsText() {
        return approvalSteps.map((step) => `${step.name}|${step.level}`).join('\n');
    }

    function renderApprovalSteps() {
        approvalStepsListElement.innerHTML = '';
        if (approvalSteps.length === 0) {
            approvalStepsListElement.innerHTML = '<p class="text-muted small mb-3">Belum ada approval step. Tambahkan minimal 1 tahap.</p>';
            return;
        }
        approvalSteps.forEach((step, index) => {
            const row = document.createElement('div');
            row.className = 'approval-step-card';
            row.innerHTML = `
                <div>
                    <label class="form-label small fw-semibold text-muted">Step ${index + 1} Name</label>
                    <input type="text" data-approval-key="name" data-approval-index="${index}" value="${step.name}" class="form-control form-control-sm">
                </div>
                <div>
                    <label class="form-label small fw-semibold text-muted">Level</label>
                    <select data-approval-key="level" data-approval-index="${index}" class="form-select form-select-sm">
                        <option value="supervisor" ${step.level === 'supervisor' ? 'selected' : ''}>Supervisor</option>
                        <option value="manager" ${step.level === 'manager' ? 'selected' : ''}>Manager</option>
                        <option value="gm" ${step.level === 'gm' ? 'selected' : ''}>GM</option>
                        <option value="director" ${step.level === 'director' ? 'selected' : ''}>Director</option>
                    </select>
                </div>
                <div class="d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm" data-approval-remove="${index}">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            `;
            approvalStepsListElement.appendChild(row);
        });
    }

    function normalizeColumns(text) {
        return text.split('\n').map((line) => line.trim()).filter((line) => line.length > 0)
            .map((line) => {
                const [nameRaw, typeRaw = 'text', extraRaw = ''] = line.split('|');
                const name = (nameRaw || '').trim();
                const type = (typeRaw || 'text').trim();
                if (!name) return null;
                if (!['text', 'number', 'dropdown', 'calc'].includes(type)) return null;
                const col = { id: uid('col'), name, type };
                if (type === 'dropdown') col.options = extraRaw.split(',').map((x) => x.trim()).filter(Boolean);
                if (type === 'calc') col.formula = (extraRaw || '').trim();
                return col;
            }).filter(Boolean);
    }

    function columnsToText(columns) {
        return (columns || []).map((col) => {
            if (col.type === 'dropdown') return `${col.name}|dropdown|${(col.options || []).join(', ')}`;
            if (col.type === 'calc') return `${col.name}|calc|${col.formula || ''}`;
            return `${col.name}|${col.type || 'text'}`;
        }).join('\n');
    }

    function renderFields() {
        fieldListElement.innerHTML = '';
        fields.forEach((field, index) => {
            const card = document.createElement('div');
            card.className = 'field-card';
            const typeOptions = ['text','textarea','number','email','date','dropdown','radio','checkbox','file','calculation','table']
                .map(([value, label]) => `<option value="${value}" ${field.type === value ? 'selected' : ''}>${label}</option>`).join('');
            const optionsInput = ['dropdown','radio','checkbox'].includes(field.type)
                ? `<div class="mb-2"><label class="form-label small fw-semibold text-muted">Options (comma separated)</label><input data-key="options" data-index="${index}" value="${(field.options || []).join(', ')}" class="form-control form-control-sm"></div>` : '';
            const formulaInput = field.type === 'calculation'
                ? `<div class="mb-2"><label class="form-label small fw-semibold text-muted">Formula</label><input data-key="formula" data-index="${index}" value="${field.formula || ''}" class="form-control form-control-sm" placeholder="{Qty} * {Price}"></div>` : '';
            const tableColumnsInput = field.type === 'table'
                ? `<div class="mb-2"><label class="form-label small fw-semibold text-muted">Table Columns</label><textarea data-key="tableColumnsText" data-index="${index}" rows="4" class="form-control form-control-sm">${columnsToText(field.tableColumns || [])}</textarea></div>` : '';
            card.innerHTML = `
                <div class="field-card-header">
                    <span class="badge bg-primary">${index + 1}. ${field.type}</span>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-remove="${index}">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-semibold text-muted">Label</label>
                    <input data-key="label" data-index="${index}" value="${field.label || ''}" class="form-control form-control-sm">
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-semibold text-muted">Type</label>
                    <select data-key="type" data-index="${index}" class="form-select form-select-sm">${typeOptions}</select>
                </div>
                <div class="form-check mb-2">
                    <input type="checkbox" data-key="required" data-index="${index}" class="form-check-input" id="req_${index}" ${field.required ? 'checked' : ''}>
                    <label class="form-check-label small fw-semibold text-muted" for="req_${index}">Required</label>
                </div>
                ${optionsInput}${formulaInput}${tableColumnsInput}
            `;
            fieldListElement.appendChild(card);
        });
    }

    function applyFieldTypeDefaults(field, nextType) {
        field.type = nextType;
        if (['dropdown','radio','checkbox'].includes(nextType)) { if (!Array.isArray(field.options) || field.options.length === 0) field.options = ['Option 1']; } else { delete field.options; }
        if (nextType === 'calculation') { field.formula = field.formula || ''; } else { delete field.formula; }
        if (nextType === 'table') {
            if (!Array.isArray(field.tableColumns) || field.tableColumns.length === 0) {
                field.tableColumns = [
                    { id: uid('col'), name: 'Item', type: 'text' },
                    { id: uid('col'), name: 'Qty', type: 'number' },
                    { id: uid('col'), name: 'Price', type: 'number' },
                    { id: uid('col'), name: 'Total', type: 'calc', formula: '{Qty} * {Price}' },
                ];
            }
        } else { delete field.tableColumns; }
    }

    function addField(type) {
        const newField = { id: uid('fld'), type, label: '', required: false };
        if (['dropdown','radio','checkbox'].includes(type)) newField.options = ['Option 1'];
        if (type === 'calculation') newField.formula = '';
        if (type === 'table') newField.tableColumns = [{ id: uid('col'), name: 'Item', type: 'text' },{ id: uid('col'), name: 'Qty', type: 'number' },{ id: uid('col'), name: 'Price', type: 'number' },{ id: uid('col'), name: 'Total', type: 'calc', formula: '{Qty} * {Price}' }];
        fields.push(newField);
        renderFields();
    }

    addFieldButton.addEventListener('click', () => addField(fieldTypeSelect.value));

    addApprovalStepButton.addEventListener('click', () => {
        const stepName = String(newApprovalStepNameInput.value || '').trim();
        const stepLevel = String(newApprovalStepLevelSelect.value || 'supervisor').toLowerCase();
        if (!stepName) { newApprovalStepNameInput.focus(); return; }
        approvalSteps.push({ name: stepName, level: stepLevel });
        newApprovalStepNameInput.value = '';
        renderApprovalSteps();
    });

    fieldListElement.addEventListener('click', (event) => {
        const removeIndex = event.target.getAttribute('data-remove');
        if (removeIndex !== null) { fields.splice(Number(removeIndex), 1); renderFields(); }
    });

    fieldListElement.addEventListener('input', (event) => {
        const index = event.target.getAttribute('data-index');
        const key = event.target.getAttribute('data-key');
        if (index === null || !key) return;
        const i = Number(index);
        if (!fields[i]) return;
        if (key === 'required') { fields[i].required = event.target.checked; return; }
        if (key === 'type') { applyFieldTypeDefaults(fields[i], event.target.value); renderFields(); return; }
        if (key === 'options') { fields[i].options = event.target.value.split(',').map((x) => x.trim()).filter(Boolean); return; }
        if (key === 'tableColumnsText') { fields[i].tableColumns = normalizeColumns(event.target.value); return; }
        fields[i][key] = event.target.value;
    });

    approvalStepsListElement.addEventListener('click', (event) => {
        const removeIndex = event.target.getAttribute('data-approval-remove');
        if (removeIndex === null) return;
        approvalSteps.splice(Number(removeIndex), 1);
        renderApprovalSteps();
    });

    approvalStepsListElement.addEventListener('input', (event) => {
        const index = event.target.getAttribute('data-approval-index');
        const key = event.target.getAttribute('data-approval-key');
        if (index === null || !key) return;
        const i = Number(index);
        if (!approvalSteps[i]) return;
        approvalSteps[i][key] = String(event.target.value || '').trim();
    });

    if (formElement) {
        formElement.addEventListener('submit', () => {
            approvalSteps = approvalSteps.map((step) => ({
                name: String(step.name || '').trim(),
                level: ['supervisor','manager','gm','director'].includes(String(step.level || '').toLowerCase()) ? String(step.level).toLowerCase() : 'supervisor',
            })).filter((step) => step.name !== '');
            if (approvalSteps.length === 0) approvalSteps = [{ name: 'Supervisor Approval', level: 'supervisor' }];
            approvalStepsTextInput.value = toApprovalStepsText();
            fieldsInput.value = JSON.stringify(fields);
        });
    }

    renderApprovalSteps();
    renderFields();
</script>
@endpush
