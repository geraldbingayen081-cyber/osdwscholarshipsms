<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ComplianceRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'compliance_request_id',
        'name',
        'instruction',
        'is_required',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function complianceRequest(): BelongsTo
    {
        return $this->belongsTo(ComplianceRequest::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ScholarComplianceDocument::class);
    }
}
