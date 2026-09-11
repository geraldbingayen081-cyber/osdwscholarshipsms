<?php

namespace App\Filament\Resources\ScholarshipResource\Pages;

use App\Filament\Resources\ScholarshipResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScholarships extends ListRecords
{
    protected static string $resource = ScholarshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['created_by'] = auth()->id() ?? 1;
                    return $data;
                }),
        ];
    }
}
