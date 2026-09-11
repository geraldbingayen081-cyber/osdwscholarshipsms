<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class ComplianceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'scholarship_id',
        'academic_year_id',
        'school_year',
        'semester',
        'title',
        'instructions',
        'deadline',
        'status',
        'created_by',
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    public function scholarship(): BelongsTo
    {
        return $this->belongsTo(Scholarship::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(ComplianceRequirement::class);
    }

    public function scholarCompliances(): HasMany
    {
        return $this->hasMany(ScholarCompliance::class);
    }

    public function isPastDeadline(): bool
    {
        return now()->startOfDay()->gt($this->deadline);
    }

    public function daysRemaining(): int
    {
        $diff = now()->startOfDay()->diffInDays($this->deadline, false);
        return (int) $diff;
    }

    public function getFormattedPeriodAttribute(): string
    {
        return "{$this->semester}, AY {$this->school_year}";
    }
}
