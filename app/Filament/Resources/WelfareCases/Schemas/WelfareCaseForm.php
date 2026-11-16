<?php

namespace App\Filament\Resources\WelfareCases\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WelfareCaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('case_id')
                    ->required()
                    ->disabled(),
                Select::make('student_id')
                    ->relationship('student', 'student_number')
                    ->required()
                    ->searchable(),
                Select::make('category')
                    ->options(\App\Models\WelfareCase::categoryOptions())
                    ->required()
                    ->default(\App\Models\WelfareCase::CATEGORY_SCHOLARSHIP),
                Select::make('status')
                    ->options([
                        'Open' => 'Open',
                        'Under Assessment' => 'Under Assessment',
                        'Referred' => 'Referred',
                        'For Follow-up' => 'For Follow-up',
                        'Resolved' => 'Resolved',
                        'Closed' => 'Closed',
                    ])
                    ->required()
                    ->default('Open'),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('requested_information')
                    ->label('OSDW Staff Notes / Requested Information')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
