<?php

namespace App\Filament\Resources;

use App\Enums\ScholarshipProvider;
use App\Filament\Resources\ScholarshipResource\Pages;
use App\Models\AcademicYear;
use App\Models\Scholarship;
use App\Models\Semester;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ScholarshipResource extends Resource
{
    protected static ?string $model = Scholarship::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static \UnitEnum|string|null $navigationGroup = 'Scholarship Administration';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                SchemaComponents\Section::make('Program Overview')
                    ->description('General information and grantor configuration')
                    ->schema([
                        Components\TextInput::make('name')
                            ->label('Scholarship Program Title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., CHED UniFAST Tertiary Education Subsidy'),

                        Components\Select::make('provider')
                            ->label('Grantor / Provider')
                            ->options([
                                'CHED' => 'Commission on Higher Education (CHED)',
                                'LGU' => 'Local Government Unit (LGU)',
                                'DOST' => 'Department of Science and Technology (DOST)',
                                'Institutional' => 'CSU Lal-lo Institutional Grant',
                                'Private' => 'Private Donor / Foundation',
                            ])
                            ->required()
                            ->searchable(),

                        Components\TextInput::make('school_year')
                            ->label('School Year')
                            ->placeholder('e.g. 2026-2027')
                            ->required()
                            ->regex('/^\d{4}-\d{4}$/')
                            ->helperText('Format: XXXX-XXXX with consecutive years (e.g. 2026-2027)'),
                    ])
                    ->columns(3),

                SchemaComponents\Section::make('Campus Rules Engine')
                    ->description('Qualification cutoffs and mutual exclusivity constraints')
                    ->schema([
                        Components\TextInput::make('min_gwa')
                            ->label('Minimum Required GWA')
                            ->numeric()
                            ->step('0.01')
                            ->minValue(1.00)
                            ->maxValue(5.00)
                            ->placeholder('e.g. 1.75 (Leave blank if no GWA cutoff)')
                            ->helperText('Lower GWA = Higher academic requirement (e.g. 1.75 is stricter than 2.25)'),

                        Components\TextInput::make('max_household_income')
                            ->label('Maximum Monthly Household Income (₱)')
                            ->numeric()
                            ->step('100.00')
                            ->prefix('₱')
                            ->placeholder('e.g. 30000.00')
                            ->helperText('Applicants exceeding this monthly income ceiling will be flagged'),

                        Components\Toggle::make('is_mutually_exclusive')
                            ->label('Is Mutually Exclusive?')
                            ->helperText('If enabled, scholars receiving this grant cannot hold another mutually exclusive scholarship')
                            ->default(false),
                    ])
                    ->columns(3),

                SchemaComponents\Section::make('Capacity & Deadlines')
                    ->schema([
                        Components\TextInput::make('available_slots')
                            ->label('Available Slots')
                            ->numeric()
                            ->required()
                            ->default(50),

                        Components\DatePicker::make('application_start_date')
                            ->label('Opening Date')
                            ->required()
                            ->default(now()),

                        Components\DatePicker::make('application_deadline')
                            ->label('Application Deadline')
                            ->required(),

                        Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'open' => 'Open for Applications',
                                'closed' => 'Closed',
                                'archived' => 'Archived',
                            ])
                            ->default('draft')
                            ->required(),

                        Components\Textarea::make('benefits')
                            ->label('Grants & Financial Benefits')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('e.g., ₱10,000 per semester stipend plus tuition coverage'),

                        Components\Textarea::make('description')
                            ->label('Full Description & Eligibility Guidelines')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Scholarship Program')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('provider')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'CHED' => 'primary',
                        'LGU' => 'success',
                        'DOST' => 'warning',
                        'Institutional' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('available_slots')
                    ->label('Slots')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('applications_count')
                    ->label('Applicants')
                    ->counts('applications')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('application_deadline')
                    ->label('Deadline')
                    ->date('M d, Y')
                    ->sortable(),

                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'open' => 'Open',
                        'closed' => 'Closed',
                        'archived' => 'Archived',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('provider')
                    ->options([
                        'CHED' => 'CHED',
                        'LGU' => 'LGU',
                        'DOST' => 'DOST',
                        'Institutional' => 'Institutional',
                        'Private' => 'Private',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'open' => 'Open',
                        'closed' => 'Closed',
                        'archived' => 'Archived',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScholarships::route('/'),
            'create' => Pages\CreateScholarship::route('/create'),
            'edit' => Pages\EditScholarship::route('/{record}/edit'),
        ];
    }

    public static function getUrl(?string $name = null, array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null, bool $shouldGuessMissingParameters = false, ?string $configuration = null): string
    {
        if ($name === 'index' || $name === null) {
            return route('admin.scholarships.index', $parameters, $isAbsolute);
        }

        return parent::getUrl($name, $parameters, $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters, $configuration);
    }
}
