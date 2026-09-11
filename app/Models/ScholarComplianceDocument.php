<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ScholarComplianceDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'scholar_compliance_id',
        'compliance_requirement_id',
        'file_path',
        'original_filename',
        'student_remarks',
        'verification_status',
        'admin_remarks',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function scholarCompliance(): BelongsTo
    {
        return $this->belongsTo(ScholarCompliance::class);
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(ComplianceRequirement::class, 'compliance_requirement_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function isImage(): bool
    {
        $extension = strtolower(pathinfo($this->original_filename, PATHINFO_EXTENSION));
        return in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
    }

    public function isPdf(): bool
    {
        $extension = strtolower(pathinfo($this->original_filename, PATHINFO_EXTENSION));
        return $extension === 'pdf';
    }
}
