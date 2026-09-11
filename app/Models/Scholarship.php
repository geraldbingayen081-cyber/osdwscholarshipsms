<?php

namespace App\Models;

use App\Enums\ScholarshipProvider;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Scholarship extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'semester_id',
        'school_year',
        'name',
        'provider',
        'description',
        'benefits',
        'min_gwa',
        'max_household_income',
        'is_mutually_exclusive',
        'available_slots',
        'application_start_date',
        'application_deadline',
        'renewal_deadline',
        'coverage_type',
        'status',
        'created_by',
    ];

    public const COVERAGE_CONTINUING = 'continuing';
    public const COVERAGE_ANNUAL = 'annual';

    public const COVERAGE_TYPES = [
        self::COVERAGE_CONTINUING => 'Continuing / Multi-Year',
        self::COVERAGE_ANNUAL => 'School Year-Based / Annual',
    ];

    public function getCoverageTypeLabelAttribute(): string
    {
        return self::COVERAGE_TYPES[$this->coverage_type] ?? 'Continuing / Multi-Year';
    }

    public function isContinuing(): bool
    {
        return $this->coverage_type === self::COVERAGE_CONTINUING;
    }

    public function isAnnual(): bool
    {
        return $this->coverage_type === self::COVERAGE_ANNUAL;
    }

    public function getSchoolYearLabelAttribute(): string
    {
        if (!empty($this->school_year)) {
            return $this->school_year;
        }

        if ($this->academicYear) {
            return str_replace('AY ', '', $this->academicYear->name);
        }

        return 'N/A';
    }

    protected $casts = [
        'min_gwa' => 'decimal:2',
        'max_household_income' => 'decimal:2',
        'is_mutually_exclusive' => 'boolean',
        'application_start_date' => 'date',
        'application_deadline' => 'date',
        'renewal_deadline' => 'date',
        'available_slots' => 'integer',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(ScholarshipRequirement::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function scholars(): HasMany
    {
        return $this->hasMany(Scholar::class);
    }

    public function complianceRequests(): HasMany
    {
        return $this->hasMany(ComplianceRequest::class);
    }

    // Accessor for title compatibility
    public function getTitleAttribute(): string
    {
        return $this->name;
    }

    public function getApprovedScholarsCountAttribute(): int
    {
        if ($this->relationLoaded('scholars')) {
            return $this->scholars->where('status', '!=', 'terminated')->count();
        }

        if (isset($this->attributes['scholars_count'])) {
            return (int) $this->attributes['scholars_count'];
        }

        return $this->scholars()->where('status', '!=', 'terminated')->count();
    }

    public function getRemainingSlotsAttribute(): int
    {
        $total = $this->available_slots ?? 0;
        $occupied = $this->approved_scholars_count;
        return max(0, $total - $occupied);
    }

    public function getSlotsDisplayAttribute(): string
    {
        if (is_null($this->available_slots)) {
            return '—';
        }
        return "{$this->remaining_slots}/{$this->available_slots}";
    }

    public function isOpenForApplication(): bool
    {
        if ($this->status !== 'open') {
            return false;
        }

        $today = now()->startOfDay();
        return $today->gte($this->application_start_date) && $today->lte($this->application_deadline);
    }

    public function isMutuallyExclusive(): bool
    {
        return (bool) $this->is_mutually_exclusive;
    }
}
