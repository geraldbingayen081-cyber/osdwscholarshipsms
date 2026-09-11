<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScholarRenewal extends Model
{
    use HasFactory;

    protected $fillable = [
        'scholar_id',
        'academic_year_id',
        'semester_id',
        'requirement_type',
        'file_path',
        'original_filename',
        'status',
        'remarks',
        'submitted_at',
        'verified_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function scholar(): BelongsTo
    {
        return $this->belongsTo(Scholar::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }
}
