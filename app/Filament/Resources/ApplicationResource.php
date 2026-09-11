<?php

namespace App\Filament\Resources;

use App\Enums\ApplicationStatus;
use App\Enums\College;
use App\Filament\Resources\ApplicationResource\Pages;
use App\Models\Application;
use App\Models\Scholar;
use App\Models\Scholarship;
use Filament\Forms\Components;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Response;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected static \UnitEnum|string|null $navigationGroup = 'Scholarship Administration';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('Application Record')
                    ->schema([
                        Components\Select::make('student_id')
                            ->relationship('student', 'student_number')
                            ->required(),

                        Components\Select::make('scholarship_id')
                            ->relationship('scholarship', 'name')
                            ->required(),

                        Components\TextInput::make('student_gwa')
                            ->label('Verified GWA')
                            ->numeric()
                            ->step('0.01'),

                        Components\TextInput::make('monthly_income')
                            ->label('Verified Household Income (₱)')
                            ->numeric()
                            ->prefix('₱'),

                        Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'submitted' => 'Submitted',
                                'under_review' => 'Under Review',
                                'deficient' => 'Deficient Document(s)',
                                'eligible' => 'Eligible',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'disbursed' => 'Disbursed',
                            ])
                            ->required(),

                        Components\Textarea::make('remarks')
                            ->label('Evaluator Remarks')
                            ->rows(3)
                            ->columnSpanFull(),

                        Components\Textarea::make('rejection_reason')
                            ->label('Deficiency / Rejection Reason')
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
                Tables\Columns\TextColumn::make('student.student_number')
                    ->label('Student ID')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('student.user.full_name')
                    ->label('Student Full Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('student.college')
                    ->label('College')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof College ? $state->value : ($state ?? 'N/A'))
                    ->color(fn ($state): string => match($state instanceof College ? $state->value : $state) {
                        'CAg' => 'primary',
                        'CHM' => 'warning',
                        'CICS' => 'info',
                        'CTE' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('scholarship.name')
                    ->label('Scholarship')
                    ->searchable()
                    ->limit(25),

                Tables\Columns\TextColumn::make('student_gwa')
                    ->label('GWA')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->student_gwa ?? $record->student?->current_gwa ?? 'N/A')
                    ->extraAttributes(['class' => 'font-mono text-center']),

                Tables\Columns\TextColumn::make('monthly_income')
                    ->label('Monthly Income')
                    ->money('PHP')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->monthly_income ?? $record->student?->monthly_household_income ?? 0),

                Tables\Columns\IconColumn::make('has_deficiencies')
                    ->label('Docs Alert')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->documents->contains(fn ($doc) => in_array($doc->status, ['needs_resubmission', 'rejected']) || $doc->verification_status === 'rejected'))
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->tooltip(fn ($record) => $record->documents->contains(fn ($doc) => in_array($doc->status, ['needs_resubmission', 'rejected']) || $doc->verification_status === 'rejected') ? 'Deficient document flagged' : 'All documents clear'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state): string => match(is_object($state) ? $state->value : $state) {
                        'draft' => 'gray',
                        'submitted' => 'warning',
                        'under_review' => 'info',
                        'deficient' => 'warning',
                        'eligible' => 'primary',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'disbursed' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', is_object($state) ? $state->value : $state))),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('college')
                    ->label('College')
                    ->options([
                        'CAg' => 'CAg (College of Agriculture)',
                        'CHM' => 'CHM (College of Hospitality Management)',
                        'CICS' => 'CICS (College of Information & Computing Sciences)',
                        'CTE' => 'CTE (College of Teacher Education)',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('student', fn ($q) => $q->where('college', $data['value']));
                        }
                    }),

                Tables\Filters\SelectFilter::make('scholarship_id')
                    ->label('Scholarship Program')
                    ->options(Scholarship::pluck('name', 'id')),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'submitted' => 'Submitted',
                        'under_review' => 'Under Review',
                        'deficient' => 'Deficient Document(s)',
                        'eligible' => 'Eligible',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'disbursed' => 'Disbursed',
                    ]),

                Tables\Filters\Filter::make('honor_students')
                    ->label('High Academic Standing (GWA ≤ 1.75)')
                    ->query(fn (Builder $query) => $query->where('student_gwa', '<=', 1.75)->orWhereHas('student', fn ($q) => $q->where('current_gwa', '<=', 1.75))),
            ])
            ->actions([
                Tables\Actions\Action::make('review')
                    ->label('Review & Inspect')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url(fn ($record) => route('filament.admin.resources.applications.review', $record)),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_eligible')
                        ->label('Batch Mark Eligible')
                        ->icon('heroicon-o-check-badge')
                        ->color('primary')
                        ->action(function (Collection $records) {
                            $records->each(fn ($record) => $record->update(['status' => 'eligible']));
                            Notification::make()
                                ->title('Applications updated to Eligible')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('approve_and_enroll')
                        ->label('Batch Approve & Move to Active Scholars')
                        ->icon('heroicon-o-academic-cap')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $count = 0;
                            foreach ($records as $app) {
                                $app->update(['status' => 'approved']);
                                Scholar::firstOrCreate([
                                    'student_id' => $app->student_id,
                                    'scholarship_id' => $app->scholarship_id,
                                ], [
                                    'application_id' => $app->id,
                                    'status' => 'active',
                                    'approved_at' => now(),
                                ]);
                                $count++;
                            }
                            Notification::make()
                                ->title("{$count} Application(s) Approved & Enrolled into Active Scholars")
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('export_csv')
                        ->label('Export Selected (CSV for CHED/LGU)')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->action(function (Collection $records) {
                            $csvHeader = ["Student ID", "Full Name", "College", "Program", "Year Level", "Scholarship", "GWA", "Monthly Household Income", "Status", "Submission Date"];
                            $lines = [implode(',', $csvHeader)];

                            foreach ($records as $app) {
                                $lines[] = implode(',', [
                                    '"' . ($app->student->student_number ?? '') . '"',
                                    '"' . ($app->student->user->full_name ?? '') . '"',
                                    '"' . ($app->student->college?->value ?? $app->student->course ?? '') . '"',
                                    '"' . ($app->student->program ?? $app->student->course ?? '') . '"',
                                    '"' . ($app->student->year_level ?? '') . '"',
                                    '"' . ($app->scholarship->name ?? '') . '"',
                                    '"' . ($app->student_gwa ?? $app->student->current_gwa ?? '') . '"',
                                    '"' . ($app->monthly_income ?? $app->student->monthly_household_income ?? 0) . '"',
                                    '"' . (is_object($app->status) ? $app->status->value : $app->status) . '"',
                                    '"' . ($app->submitted_at ? $app->submitted_at->format('Y-m-d H:i') : '') . '"',
                                ]);
                            }

                            $csvData = implode("\n", $lines);
                            return Response::make($csvData, 200, [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="csu_lallo_osdw_applications_' . date('Ymd_His') . '.csv"',
                            ]);
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplications::route('/'),
            'edit' => Pages\EditApplication::route('/{record}/edit'),
            'review' => Pages\ReviewApplication::route('/{record}/review'),
        ];
    }

    public static function getUrl(?string $name = null, array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null, bool $shouldGuessMissingParameters = false, ?string $configuration = null): string
    {
        if ($name === 'index' || $name === null) {
            return route('admin.applications.index', $parameters, $isAbsolute);
        }

        return parent::getUrl($name, $parameters, $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters, $configuration);
    }
}
