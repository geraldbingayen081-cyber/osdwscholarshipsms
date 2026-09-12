<?php

namespace App\Models;

use App\Enums\College;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_number',
        'college',
        'program',
        'course',
        'year_level',
        'contact_number',
        'current_gwa',
        'monthly_household_income',
        'municipality',
        'barangay',
        'is_4ps',
    ];

    protected $casts = [
        'college' => College::class,
        'current_gwa' => 'decimal:2',
        'monthly_household_income' => 'decimal:2',
        'is_4ps' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function scholars(): HasMany
    {
        return $this->hasMany(Scholar::class);
    }

    // Helper Accessors
    public function getCollegeNameAttribute(): string
    {
        return $this->college?->name() ?? ($this->course ?? 'N/A');
    }

    public function getProgramDisplayAttribute(): string
    {
        return $this->program ?? ($this->course ?? 'N/A');
    }

    /**
     * Check if student currently holds an active or for_renewal scholarship grant.
     */
    public function hasActiveScholarship(): bool
    {
        return $this->scholars()->whereIn('status', ['active', 'for_renewal'])->exists();
    }

    /**
     * Get the student's active scholar grant record.
     */
    public function activeScholarship(): ?Scholar
    {
        return $this->scholars()->whereIn('status', ['active', 'for_renewal'])->with('scholarship')->first();
    }
}

