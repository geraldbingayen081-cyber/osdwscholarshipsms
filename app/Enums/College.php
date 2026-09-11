<?php

namespace App\Enums;

enum College: string
{
    case CAG = 'CAg';
    case CHM = 'CHM';
    case CICS = 'CICS';
    case CTE = 'CTE';

    public function name(): string
    {
        return match($this) {
            self::CAG => 'College of Agriculture',
            self::CHM => 'College of Hospitality Management',
            self::CICS => 'College of Information and Computing Sciences',
            self::CTE => 'College of Teacher Education',
        };
    }

    public function programs(): array
    {
        return match($this) {
            self::CAG => [
                'BS Agriculture (Crop Science)',
                'BS Agriculture (Animal Science)',
                'Diploma in Agricultural Technology - Bachelor in Agricultural Technology (DAT-BAT)',
            ],
            self::CHM => [
                'BS Hospitality Management (BSHM)',
            ],
            self::CICS => [
                'BS Information Technology (BSIT)',
            ],
            self::CTE => [
                'Bachelor of Elementary Education (BEEd)',
                'Bachelor of Secondary Education - English (BSEd-ENG)',
                'Bachelor of Secondary Education - Mathematics (BSEd-MATH)',
                'Bachelor of Secondary Education - Science (BSEd-SCI)',
                'Bachelor of Secondary Education - Filipino (BSEd-FIL)',
                'Bachelor of Secondary Education - Social Studies (BSEd-SOC)',
            ],
        };
    }

    public static function allPrograms(): array
    {
        $programs = [];
        foreach (self::cases() as $college) {
            foreach ($college->programs() as $p) {
                $programs[$p] = $p . ' (' . $college->value . ')';
            }
        }
        return $programs;
    }
}
