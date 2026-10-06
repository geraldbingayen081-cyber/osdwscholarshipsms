<?php

namespace App\Filament\Resources\WelfareCases\Pages;

use App\Filament\Resources\WelfareCases\WelfareCaseResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditWelfareCase extends EditRecord
{
    protected static string $resource = WelfareCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
