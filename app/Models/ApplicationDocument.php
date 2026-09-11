<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'scholarship_requirement_id',
        'file_path',
        'original_filename',
        'status',
        'remarks',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(ScholarshipRequirement::class, 'scholarship_requirement_id');
    }

    // Helper Methods
    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function isNeedsResubmission(): bool
    {
        return $this->status === 'needs_resubmission';
    }
}
