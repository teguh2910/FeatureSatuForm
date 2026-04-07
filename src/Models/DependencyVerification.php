<?php

namespace Teguh\FeatureSatuForm\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DependencyVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_id',
        'form_template_id',
        'dependency_form_template_id',
        'submission_id',
        'form_code',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];
}