<?php

namespace App\Filament\Clinic\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Clinic;
use App\Models\Inquery;
use App\Models\Patient;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Infolists\Components\Tabs;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Components\ImageEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clinic\Resources\InqueryResource\Pages;
use Filament\Infolists\Components\Section as InfoSection;
use App\Filament\Clinic\Resources\InqueryResource\RelationManagers;

class InqueryResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?string $modelLabel = 'Inquery';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('animal.name')->label('Pet name')->formatStateUsing(function (Patient $record) {
                    return ucfirst($record->animal?->name);
                })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('animal', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                    }),
                    TextColumn::make('animal.category.name')->label('Pet type')->formatStateUsing(function (Patient $record) {
                        return ucfirst($record->animal?->category?->name);
                    }),
                    // TextColumn::make('animal.breed')->label('Pet Breed')->formatStateUsing(function (Patient $record) {
                    //     return ucfirst($record->animal?->breed);
                    // }),
                TextColumn::make('animal')->label('Owner name')->formatStateUsing(function (Patient $record) {
                    return ucfirst($record->animal?->user?->first_name . ' ' . $record->animal?->user?->last_name);
                })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('animal.user', function ($query) use ($search) {
                            $query->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    }),

                TextColumn::make('examinations.exam_type')
                    ->listWithLineBreaks()

                    ->badge()
                    ->color('success')
                    ->label('Examination'),

                TextColumn::make('examinations.prescriptions.drug')
                    ->listWithLineBreaks()
                    ->badge()
                    ->color('info')
                    ->label('Prescriptions')
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->wrap(),
                TextColumn::make('examinations.diagnosis')

                    ->color('success')
                    ->label('Diagnosis')
                    ->wrap()
                    ->limit(50),
                TextColumn::make('created_at')
                    ->formatStateUsing(function ($record) {

                        if ($record->appointment) {

                            return $record->updated_at->format('h:i A F d, Y ') . ' - Appointment';
                            // return $record->updated_at->format('F d, Y h:i A') . ' - ' . optional($record->clinic)->name;
                        }
                        return $record->updated_at->format('h:i A F d, Y ') . ' - ' . optional($record->clinic)->name;
                    })
                    ->wrap()
                    ->label('Recorded'),

            ])
            ->filters([

                Tables\Filters\SelectFilter::make('clinic_id')
                ->label('Clinic')
                ->options(Clinic::where('id', '!=', auth()->user()->clinic?->id)->pluck('name', 'id')),

            ])
            ->actions([
                Tables\Actions\ViewAction::make()->color('primary')->button()->outlined(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->where('clinic_id', '!=', auth()->user()->clinic?->id))
            ;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Appointment Information')
                    ->tabs([
                        Tabs\Tab::make('Overview')
                            ->icon('heroicon-m-identification')
                            ->schema([
                                InfoSection::make('Clinic & Schedule')


                                    ->columns([
                                        'sm' => 3,
                                        'xl' => 6,
                                        '2xl' => 8,
                                    ])
                                    ->schema([

                                        TextEntry::make('appointment.status')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 8,
                                                '2xl' => 8,
                                            ])
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'Accepted' => 'success',
                                                'Pending' => 'info',
                                                'Completed' => 'success',
                                                'Rejected' => 'danger',
                                            })
                                            ->label('Request Status'),


                                        TextEntry::make('appointment.clinic.name')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 2,
                                            ])
                                            ->label('Clinic'),


                                        TextEntry::make('appointment.date')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 2,
                                            ])
                                            ->date()
                                            ->label('Date Schedule'),



                                        TextEntry::make('appointment.time')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 2,
                                            ])

                                            ->date('H:i:s A')->timeZone('Asia/Manila')
                                            ->label('Time Schedule'),


                                        TextEntry::make('appointment.extra_pet_info')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 8,
                                            ])

                                            ->markdown()
                                            ->label('Extra Details'),

                                    ])->hidden(function ($record) {
                                        if ($record->appointment) {
                                            return false;
                                        }
                                        return true;
                                    }),
                                InfoSection::make('Owner ')


                                    ->columns([
                                        'sm' => 3,
                                        'xl' => 6,
                                        '2xl' => 8,
                                    ])
                                    ->schema([
                                        TextEntry::make('animal.user')->columnSpan(6)->label('Owner')
                                            ->formatStateUsing(fn (Patient $record): string => ucfirst($record?->animal?->user?->first_name . '' . $record?->animal?->user?->last_name))
                                            ->label('Name')
                                            ->columnSpan([
                                                'sm' => 2,
                                                'xl' => 3,
                                                '2xl' => 4,
                                            ])->color('gray'),


                                        TextEntry::make('animal.user')->columnSpan(6)->label('Phone Number')
                                            ->formatStateUsing(fn (Patient $record): string => !empty($record->phone_number) ? $record->phone_number : 'N/S')

                                            ->columnSpan([
                                                'sm' => 2,
                                                'xl' => 3,
                                                '2xl' => 4,
                                            ])->color('gray'),
                                        TextEntry::make('animal.user')->columnSpan(6)->label('Address')
                                            ->formatStateUsing(fn (Patient $record): string => !empty($record->address) ? $record->address : 'N/S')

                                            ->columnSpan([
                                                'sm' => 2,
                                                'xl' => 3,
                                                '2xl' => 4,
                                            ])->color('gray'),
                                        TextEntry::make('animal.user.email')->columnSpan(6)->label('Email')
                                            ->columnSpan([
                                                'sm' => 2,
                                                'xl' => 3,
                                                '2xl' => 4,
                                            ])->color('gray'),

                                    ]),
                                InfoSection::make('Pet Details')

                                    ->schema([

                                        ImageEntry::make('animal.image')
                                            ->height(300)
                                            ->disk('public')
                                            ->url(fn ($state): string =>  $state ? Storage::url($state) : null)
                                            ->openUrlInNewTab()
                                            ->label('Pet Profile Image ')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 8,
                                            ]),

                                        TextEntry::make('animal.name')
                                            ->label('Name')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 2,
                                            ])->color('gray'),
                                        TextEntry::make('animal.breed')
                                            ->label('Breed')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 2,
                                            ])->color('gray'),
                                        TextEntry::make('animal.sex')
                                            ->label('Sex')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 2,
                                            ])->color('gray'),
                                        TextEntry::make('animal.date_of_birth')
                                            ->date()
                                            ->label('Birth date')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 2,
                                            ])->color('gray'),

                                        TextEntry::make('animal.weight')
                                            ->label('Weight')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 2,
                                            ])->color('gray'),



                                    ]),
                            ]),
                        Tabs\Tab::make('Examination & Prescriptions')

                            ->icon('heroicon-m-sparkles')
                            ->schema([
                                // InfoSection::make('Services')
                                //     ->schema([
                                //         ViewEntry::make('services')->columnSpan([
                                //             'sm' => 1,
                                //             'xl' => 2,
                                //             '2xl' => 8,
                                //         ])
                                //             ->label('Requested Services *')

                                //             ->view('infolists.components.services-list'),
                                //     ])->hidden(fn($record)=> $record->clinic_id != auth()->user()->clinic?->id ? true :false),



                                InfoSection::make('Examination')
                                    ->schema([
                                        TextEntry::make('examination.exam_type')->color('gray')->label('Exam Type'),
                                        TextEntry::make('examination.examination_date')->color('gray')->label('Examine Date'),
                                        TextEntry::make('examination.temperature')->color('gray')->label('Temperature'),
                                        TextEntry::make('examination.crt')->color('gray')->label('Cry'),
                                        TextEntry::make('examination.diagnosis')->color('gray')->label('Diagnosis')->columnSpanFull(),
                                        TextEntry::make('examination.exam_result')->color('gray')->label('Exam Result')->columnSpanFull(),
                                        ImageEntry::make('examination.image_result')
                                            ->disk('public')
                                            ->url(fn ($state): string =>  $state ? Storage::url($state) : null)
                                            ->openUrlInNewTab()
                                            ->label('Image Result')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 8,
                                            ]),


                                    ])->hidden(function (Patient $record) {
                                        if ($record->examination) {
                                            return false;
                                        }
                                        return true;
                                    }),

                                InfoSection::make('Prescriptions')
                                    ->schema([

                                        ViewEntry::make('examination.prescriptions')
                                            ->view('infolists.components.prescriptions-entry')->label('Prescriptions')



                                    ])->hidden(function (Patient $record) {
                                        if ($record->examination) {
                                            return false;
                                        }
                                        return true;
                                    }),
                            ]),

                    ])
                    ->columnSpan(8),

            ]);
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
            'index' => Pages\ListInqueries::route('/'),
            'create' => Pages\CreateInquery::route('/create'),
            'edit' => Pages\EditInquery::route('/{record}/edit'),
        ];
    }    
}
