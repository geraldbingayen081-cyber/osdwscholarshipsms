<?php

namespace App\Filament\Resources\WelfareCases\Pages;

use App\Filament\Resources\WelfareCases\WelfareCaseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWelfareCases extends ListRecords
{
    protected static string $resource = WelfareCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
