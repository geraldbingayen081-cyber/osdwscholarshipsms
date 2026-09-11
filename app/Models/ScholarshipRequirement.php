<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScholarshipRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'scholarship_id',
        'requirement_name',
        'instructions',
        'requirement_type',
        'deadline',
        'status',
        'semester',
        'school_year',
        'is_required',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'deadline' => 'date',
    ];

    // ─── Status Helpers ──────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    // ─── Query Scopes ─────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function scholarship(): BelongsTo
    {
        return $this->belongsTo(Scholarship::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class);
    }
}
