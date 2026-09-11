<?php

namespace App\Enums;

enum ScholarshipProvider: string
{
    case CHED = 'CHED';
    case LGU = 'LGU';
    case DOST = 'DOST';
    case INSTITUTIONAL = 'Institutional';
    case PRIVATE = 'Private';

    public function label(): string
    {
        return match($this) {
            self::CHED => 'Commission on Higher Education (CHED)',
            self::LGU => 'Local Government Unit (LGU)',
            self::DOST => 'Department of Science and Technology (DOST)',
            self::INSTITUTIONAL => 'CSU Lal-lo Institutional Grant',
            self::PRIVATE => 'Private Donor / Foundation',
        };
    }
}
