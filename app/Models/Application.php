<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'scholarship_id',
        'student_gwa',
        'monthly_income',
        'status',
        'remarks',
        'submitted_at',
    ];

    protected $casts = [
        'student_gwa' => 'decimal:2',
        'monthly_income' => 'decimal:2',
        'submitted_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function scholarship(): BelongsTo
    {
        return $this->belongsTo(Scholarship::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    public function scholar(): HasOne
    {
        return $this->hasOne(Scholar::class);
    }

    // Status Enum & Label Accessors
    public function getStatusEnumAttribute(): ?ApplicationStatus
    {
        return ApplicationStatus::tryFrom($this->attributes['status'] ?? '');
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status_enum?->label() ?? ucfirst(str_replace('_', ' ', (string) ($this->attributes['status'] ?? '')));
    }

    // State Machine Helper Methods
    public function isDeficient(): bool
    {
        return $this->attributes['status'] === 'deficient';
    }

    public function canBeEdited(): bool
    {
        return in_array($this->attributes['status'] ?? '', ['draft', 'deficient']);
    }

    public function isApproved(): bool
    {
        return $this->attributes['status'] === 'approved';
    }

    public function isDisbursed(): bool
    {
        return $this->attributes['status'] === 'disbursed';
    }

    public function isEligible(): bool
    {
        return $this->attributes['status'] === 'eligible';
    }
}
