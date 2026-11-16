<?php

namespace App\Models;

use App\Notifications\WelfareCaseStatusUpdated;
use Illuminate\Database\Eloquent\Model;

class WelfareCase extends Model
{
    protected $fillable = [
        'case_id',
        'student_id',
        'category',
        'description',
        'requested_information',
        'status',
    ];

    public const CATEGORY_SCHOLARSHIP = 'Scholarship and Financial Assistance';
    public const CATEGORY_ASSISTANTSHIP = 'Student Assistantship';
    public const CATEGORY_SMAP = 'Student Mutual Aid Program (SMAP)';
    public const CATEGORY_CO_CURRICULAR = 'Co-Curricular Programs Incentives';
    public const CATEGORY_TOPNOTCHERS = 'Incentives for National Topnotchers in Board Examinations';
    public const CATEGORY_ORG_ACCREDITATION = 'Student Organizations Accreditation / Re-Accreditation';
    public const CATEGORY_ORG_ADVISING = 'Advising of Student Organizations';

    public const CATEGORIES = [
        self::CATEGORY_SCHOLARSHIP,
        self::CATEGORY_ASSISTANTSHIP,
        self::CATEGORY_SMAP,
        self::CATEGORY_CO_CURRICULAR,
        self::CATEGORY_TOPNOTCHERS,
        self::CATEGORY_ORG_ACCREDITATION,
        self::CATEGORY_ORG_ADVISING,
    ];

    public static function categoryOptions(): array
    {
        return [
            self::CATEGORY_SCHOLARSHIP => '🎓 Scholarship and Financial Assistance',
            self::CATEGORY_ASSISTANTSHIP => '💼 Student Assistantship',
            self::CATEGORY_SMAP => '🛡️ Student Mutual Aid Program (SMAP)',
            self::CATEGORY_CO_CURRICULAR => '🏆 Co-Curricular Programs Incentives',
            self::CATEGORY_TOPNOTCHERS => '⭐ Incentives for National Topnotchers in Board Examinations',
            self::CATEGORY_ORG_ACCREDITATION => '🏛️ Student Organizations Accreditation / Re-Accreditation',
            self::CATEGORY_ORG_ADVISING => '🧭 Advising of Student Organizations',
        ];
    }

    public static function defaultOfficeForCategory(?string $category): string
    {
        return match ($category) {
            self::CATEGORY_SCHOLARSHIP => 'OSDW Scholarship & Financial Assistance Unit',
            self::CATEGORY_ASSISTANTSHIP => 'OSDW Student Assistantship In-Charge',
            self::CATEGORY_SMAP => 'OSDW Student Mutual Aid Program (SMAP) Unit',
            self::CATEGORY_CO_CURRICULAR => 'OSDW Student Development / Sports & Socio-Cultural Affairs',
            self::CATEGORY_TOPNOTCHERS => 'Office of Academic Affairs / OSDW Honors & Awards Committee',
            self::CATEGORY_ORG_ACCREDITATION => 'OSDW Student Organizations & Activities Unit (SOAU)',
            self::CATEGORY_ORG_ADVISING => 'OSDW Student Organizations & Activities Unit (SOAU)',
            default => 'OSDW Student Welfare Division',
        };
    }

    public static function defaultReferralTypeForCategory(?string $category): string
    {
        return $category === self::CATEGORY_SCHOLARSHIP ? 'scholarship' : 'office';
    }

    protected static function booted()
    {
        static::updated(function (WelfareCase $welfareCase) {
            if ($welfareCase->wasChanged(['status', 'requested_information'])) {
                if ($welfareCase->status !== 'Referred') {
                    if ($welfareCase->student && $welfareCase->student->user) {
                        $welfareCase->student->user->notify(new WelfareCaseStatusUpdated($welfareCase));
                    }
                }
            }
        });
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function documents()
    {
        return $this->hasMany(WelfareCaseDocument::class);
    }

    public function referrals()
    {
        return $this->hasMany(WelfareCaseReferral::class);
    }
}
