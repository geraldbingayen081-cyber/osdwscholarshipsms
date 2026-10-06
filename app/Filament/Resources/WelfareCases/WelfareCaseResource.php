<?php

namespace App\Filament\Resources\WelfareCases;

use App\Filament\Resources\WelfareCases\Pages\CreateWelfareCase;
use App\Filament\Resources\WelfareCases\Pages\EditWelfareCase;
use App\Filament\Resources\WelfareCases\Pages\ListWelfareCases;
use App\Filament\Resources\WelfareCases\Pages\ViewWelfareCase;
use App\Filament\Resources\WelfareCases\Schemas\WelfareCaseForm;
use App\Filament\Resources\WelfareCases\Schemas\WelfareCaseInfolist;
use App\Filament\Resources\WelfareCases\Tables\WelfareCasesTable;
use App\Models\WelfareCase;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WelfareCaseResource extends Resource
{
    protected static ?string $model = WelfareCase::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'case_id';

    public static function form(Schema $schema): Schema
    {
        return WelfareCaseForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WelfareCaseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WelfareCasesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWelfareCases::route('/'),
            'create' => CreateWelfareCase::route('/create'),
            'view' => ViewWelfareCase::route('/{record}'),
            'edit' => EditWelfareCase::route('/{record}/edit'),
        ];
    }
}
