<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScholarCompliance extends Model
{
    use HasFactory;

    protected $fillable = [
        'compliance_request_id',
        'scholar_id',
        'status',
        'submitted_at',
        'completed_at',
        'admin_notes',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function complianceRequest(): BelongsTo
    {
        return $this->belongsTo(ComplianceRequest::class);
    }

    public function scholar(): BelongsTo
    {
        return $this->belongsTo(Scholar::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ScholarComplianceDocument::class);
    }

    /**
     * Recalculates and updates the overall status of this compliance based on requirements and uploaded documents.
     */
    public function recalculateStatus(): string
    {
        $this->loadMissing(['complianceRequest.requirements', 'documents']);

        $requirements = $this->complianceRequest->requirements;
        $totalRequired = $requirements->where('is_required', true)->count();
        $docs = $this->documents;

        if ($docs->isEmpty()) {
            if ($this->complianceRequest->isPastDeadline()) {
                $newStatus = 'overdue';
            } else {
                $newStatus = 'not_submitted';
            }
            $this->update(['status' => $newStatus, 'completed_at' => null]);
            return $newStatus;
        }

        // Check if any document needs correction
        $hasNeedsCorrection = $docs->contains(function ($doc) {
            return $doc->verification_status === 'needs_correction';
        });

        if ($hasNeedsCorrection) {
            $newStatus = 'needs_correction';
            $this->update(['status' => $newStatus, 'completed_at' => null]);
            return $newStatus;
        }

        // Count verified required documents
        $verifiedRequiredCount = 0;
        foreach ($requirements as $req) {
            if ($req->is_required) {
                $doc = $docs->firstWhere('compliance_requirement_id', $req->id);
                if ($doc && $doc->verification_status === 'verified') {
                    $verifiedRequiredCount++;
                }
            }
        }

        // If all required documents are verified -> Completed!
        if ($totalRequired > 0 && $verifiedRequiredCount >= $totalRequired) {
            $newStatus = 'completed';
            $this->update([
                'status' => $newStatus,
                'completed_at' => $this->completed_at ?? now(),
            ]);
            return $newStatus;
        }

        // If all required documents have been uploaded -> Submitted / Under Review
        $uploadedRequiredCount = 0;
        foreach ($requirements as $req) {
            if ($req->is_required) {
                $doc = $docs->firstWhere('compliance_requirement_id', $req->id);
                if ($doc) {
                    $uploadedRequiredCount++;
                }
            }
        }

        if ($totalRequired > 0 && $uploadedRequiredCount >= $totalRequired) {
            $newStatus = 'under_review';
        } else {
            if ($this->complianceRequest->isPastDeadline()) {
                $newStatus = 'overdue';
            } else {
                $newStatus = 'partially_submitted';
            }
        }

        $this->update(['status' => $newStatus, 'completed_at' => null]);
        return $newStatus;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'not_submitted' => 'Not Submitted',
            'partially_submitted' => 'Partially Submitted',
            'submitted' => 'Submitted',
            'under_review' => 'Under Review',
            'completed' => 'Completed',
            'overdue' => 'Overdue',
            'needs_correction' => 'Needs Correction',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }
}
