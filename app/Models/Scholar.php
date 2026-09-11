<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Scholar extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'scholarship_id',
        'application_id',
        'status',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function scholarship(): BelongsTo
    {
        return $this->belongsTo(Scholarship::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function renewals(): HasMany
    {
        return $this->hasMany(ScholarRenewal::class);
    }

    public function compliances(): HasMany
    {
        return $this->hasMany(ScholarCompliance::class);
    }
}
