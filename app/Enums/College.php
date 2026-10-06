<?php

namespace App\Enums;

enum College: string
{
    case COA = 'COA';
    case CHM = 'CHM';
    case CICS = 'CICS';
    case CTED = 'CTED';

    public function name(): string
    {
        return match($this) {
            self::COA => 'College of Agriculture (COA)',
            self::CHM => 'College of Hospitality Management (CHM)',
            self::CICS => 'College of Information and Computing Sciences (CICS)',
            self::CTED => 'College of Teacher Education (CTED)',
        };
    }

    public function programs(): array
    {
        return match($this) {
            self::COA => [
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
            self::CTED => [
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
