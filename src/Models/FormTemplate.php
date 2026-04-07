<?php

namespace Teguh\FeatureSatuForm\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_code',
        'name',
        'description',
        'fields_config',
        'approval_flow_config',
        'dependency_form_code',
        'department',
        'is_published',
    ];

    protected $casts = [
        'fields_config' => 'array',
        'approval_flow_config' => 'array',
        'is_published' => 'boolean',
    ];
}
