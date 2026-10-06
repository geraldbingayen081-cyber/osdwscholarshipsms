<?php

namespace App\Filament\Resources\WelfareCases\Pages;

use App\Filament\Resources\WelfareCases\WelfareCaseResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewWelfareCase extends ViewRecord
{
    protected static string $resource = WelfareCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
