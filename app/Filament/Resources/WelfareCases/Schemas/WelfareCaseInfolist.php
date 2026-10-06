<?php

namespace App\Filament\Resources\WelfareCases\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class WelfareCaseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Infolists\Components\Section::make('Case Details')
                    ->schema([
                        TextEntry::make('case_id'),
                        TextEntry::make('student.user.full_name')->label('Student'),
                        TextEntry::make('category'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Open' => 'warning',
                                'Under Assessment' => 'info',
                                'Referred' => 'success',
                                'For Follow-up' => 'warning',
                                'Resolved' => 'success',
                                'Closed' => 'gray',
                                default => 'gray',
                            }),
                        TextEntry::make('description')
                            ->columnSpanFull(),
                        TextEntry::make('requested_information')
                            ->placeholder('No additional information requested.')
                            ->columnSpanFull(),
                    ])->columns(2),

                \Filament\Infolists\Components\Section::make('Related Student Records')
                    ->schema([
                        \Filament\Infolists\Components\ViewEntry::make('related_records')
                            ->view('filament.resources.welfare-case-resource.related-records')
                            ->columnSpanFull()
                    ])
            ]);
    }
}
