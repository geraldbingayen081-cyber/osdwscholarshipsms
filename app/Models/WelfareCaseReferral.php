<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WelfareCaseReferral extends Model
{
    protected $fillable = [
        'welfare_case_id',
        'scholarship_id',
        'referral_type',
        'referred_to_office',
        'referral_note',
    ];

    public function welfareCase()
    {
        return $this->belongsTo(WelfareCase::class);
    }

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }

    public function application()
    {
        return $this->hasOne(Application::class);
    }

    public function isScholarshipReferral(): bool
    {
        return $this->referral_type === 'scholarship' || !empty($this->scholarship_id);
    }

    public function getRecipientNameAttribute(): string
    {
        if ($this->isScholarshipReferral()) {
            return $this->scholarship->name ?? 'Scholarship Program';
        }
        return $this->referred_to_office ?? 'University Department / Office';
    }
}
