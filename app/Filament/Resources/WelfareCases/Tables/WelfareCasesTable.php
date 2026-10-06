<?php

namespace App\Filament\Resources\WelfareCases\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WelfareCasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('case_id')
                    ->searchable(),
                TextColumn::make('student.user.full_name')
                    ->label('Student Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->badge()
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Open' => 'warning',
                        'Under Assessment' => 'info',
                        'Referred' => 'success',
                        'For Follow-up' => 'warning',
                        'Resolved' => 'success',
                        'Closed' => 'gray',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                \Filament\Tables\Actions\Action::make('refer')
                    ->label('Issue Referral')
                    ->icon('heroicon-o-academic-cap')
                    ->color('success')
                    ->visible(fn (\App\Models\WelfareCase $record) => in_array($record->status, ['Open', 'Under Assessment']))
                    ->form([
                        \Filament\Forms\Components\Radio::make('referral_type')
                            ->label('Referral Channel')
                            ->options([
                                'scholarship' => 'Scholarship Program',
                                'office' => 'University Office / Department',
                            ])
                            ->default('scholarship')
                            ->live()
                            ->required(),
                        \Filament\Forms\Components\Select::make('scholarship_id')
                            ->label('Scholarship Program')
                            ->options(\App\Models\Scholarship::query()->pluck('name', 'id'))
                            ->visible(fn (\Filament\Forms\Get $get) => $get('referral_type') === 'scholarship')
                            ->required(fn (\Filament\Forms\Get $get) => $get('referral_type') === 'scholarship'),
                        \Filament\Forms\Components\Select::make('referred_to_office')
                            ->label('University Office / Department')
                            ->options([
                                'University Guidance & Counseling Center' => 'University Guidance & Counseling Center',
                                'Campus Clinic & Health Services' => 'Campus Clinic & Health Services',
                                'College Dean / Department Chairperson' => 'College Dean / Department Chairperson',
                                'Student Disciplinary Tribunal / Prefect of Discipline' => 'Student Disciplinary Tribunal / Prefect of Discipline',
                                'Campus Dormitory / Housing Office' => 'Campus Dormitory / Housing Office',
                                'OSDW Student Welfare Division' => 'OSDW Student Welfare Division',
                            ])
                            ->visible(fn (\Filament\Forms\Get $get) => $get('referral_type') === 'office')
                            ->required(fn (\Filament\Forms\Get $get) => $get('referral_type') === 'office'),
                        \Filament\Forms\Components\Textarea::make('referral_note')
                            ->label('Referral Endorsement & Staff Note')
                            ->required(),
                    ])
                    ->action(function (array $data, \App\Models\WelfareCase $record): void {
                        $referral = $record->referrals()->create([
                            'referral_type' => $data['referral_type'],
                            'scholarship_id' => $data['referral_type'] === 'scholarship' ? $data['scholarship_id'] : null,
                            'referred_to_office' => $data['referral_type'] === 'office' ? $data['referred_to_office'] : null,
                            'referral_note' => $data['referral_note'],
                        ]);
                        $record->update(['status' => 'Referred']);

                        if ($record->student && $record->student->user) {
                            $record->student->user->notify(new \App\Notifications\WelfareCaseReferralCreated($referral));
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Referral Issued Successfully')
                            ->body('Referral notification sent to student.')
                            ->success()
                            ->send();
                    })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
