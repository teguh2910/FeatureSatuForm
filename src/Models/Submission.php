<?php

namespace Teguh\FeatureSatuForm\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $table = 'FORM.submissions';

    protected $fillable = [
        'tracking_id',
        'template_id',
        'employee_name',
        'employee_email',
        'department',
        'form_type',
        'notes',
        'form_data',
        'status',
        'current_approval_step',
        'approval_flow_snapshot',
        'approval_history',
        'submitted_at',
    ];

    protected $casts = [
        'form_data' => 'array',
        'approval_flow_snapshot' => 'array',
        'approval_history' => 'array',
        'submitted_at' => 'datetime',
    ];
}
