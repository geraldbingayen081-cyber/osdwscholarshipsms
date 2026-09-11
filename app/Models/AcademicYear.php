<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get semesters in this academic year.
     */
    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class);
    }

    /**
     * Get scholarships in this academic year.
     */
    public function scholarships(): HasMany
    {
        return $this->hasMany(Scholarship::class);
    }
}
