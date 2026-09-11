<?php

namespace App\Filament\Resources\ScholarshipResource\Pages;

use App\Filament\Resources\ScholarshipResource;
use Filament\Resources\Pages\CreateRecord;

class CreateScholarship extends CreateRecord
{
    protected static string $resource = ScholarshipResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id() ?? 1;
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return route('admin.scholarships.index');
    }
}
