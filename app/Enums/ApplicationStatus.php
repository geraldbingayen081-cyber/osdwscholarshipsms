<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case UNDER_REVIEW = 'under_review';
    case DEFICIENT = 'deficient';
    case ELIGIBLE = 'eligible';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case DISBURSED = 'disbursed';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Submitted',
            self::UNDER_REVIEW => 'Under Review',
            self::DEFICIENT => 'Deficient Document(s)',
            self::ELIGIBLE => 'Eligible',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::DISBURSED => 'Disbursed',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::SUBMITTED => 'amber',
            self::UNDER_REVIEW => 'info',
            self::DEFICIENT => 'warning',
            self::ELIGIBLE => 'primary',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::DISBURSED => 'emerald',
        };
    }

    public function canBeEdited(): bool
    {
        return in_array($this, [self::DRAFT, self::DEFICIENT]);
    }

    public function isDeficient(): bool
    {
        return $this === self::DEFICIENT;
    }
}
