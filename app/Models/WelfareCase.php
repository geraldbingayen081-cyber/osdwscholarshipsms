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
