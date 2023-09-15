<?php

namespace App\Filament\Client\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Clinic;
use App\Models\Patient;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Infolists\Components\Tabs;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Client\Resources\PatientResource\Pages;
use Filament\Infolists\Components\Section as InfoSection;
use App\Filament\Client\Resources\PatientResource\RelationManagers;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-plus';

    protected static ?string $modelLabel = 'Medical Record';
    

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
                TextColumn::make('clinic.name')->label('Clinic')->badge('primary')->searchable(),
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
                TextColumn::make('animal.breed')->label('Pet type')->formatStateUsing(function (Patient $record) {
                    return ucfirst($record->animal?->breed);
                })->label('breed'),
           
                // TextColumn::make('animal')->label('Owner')->formatStateUsing(function (Patient $record) {
                //     return ucfirst($record->animal?->user?->first_name . ' ' . $record->animal?->user?->last_name);
                // })
                //     ->searchable(query: function (Builder $query, string $search): Builder {
                //         return $query->whereHas('animal.user', function ($query) use ($search) {
                //             $query->where('first_name', 'like', "%{$search}%")
                //                 ->orWhere('last_name', 'like', "%{$search}%");
                //         });
                //     }),

              
                TextColumn::make('created_at')
                    ->formatStateUsing(function ($record) {

                        if ($record->appointment) {

                            return $record->updated_at->format('M-d-Y h:i A') . ' - Appointment';
                            // return $record->updated_at->format('F d, Y h:i A') . ' - ' . optional($record->clinic)->name;
                        }
                        return $record->updated_at->format('M-d-Y h:i A') . ' - ' . optional($record->clinic)->name;
                    })
                    ->wrap()
                    ->label('Recorded'),
            ])
            ->filters([
                SelectFilter::make('clinic_id')
                ->multiple()
                ->options(Clinic::query()->pluck('name', 'id'))->label('By Clinic'),
                Filter::make('created_at')
                ->form([
                    DatePicker::make('created_from'),
                    DatePicker::make('created_until'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['created_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['created_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        );
                })
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()->color('primary')->modalWidth('7xl')->label('View Details'),
                    Tables\Actions\Action::make('Download')->label('Download Record')->icon('heroicon-s-arrow-down-tray')->color('primary')
                    ->url(function(Patient $record){
                        return route('download-medical-record', $record->id);
                    })
                    ->openUrlInNewTab()
                    ->hidden(function(Patient $record){
                        if($record->patient){
                            return false;
                        }else{

                            return false;
                        }
                    }),

                    Tables\Actions\DeleteAction::make()

                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('animal.user', function($query){
                $query->where('user_id', auth()->user()->id);
            }));
    }

    
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Record')
                    ->tabs([
                        Tabs\Tab::make('Appointment Schedule')
                            ->icon('heroicon-m-calendar-days')
                            ->iconPosition(IconPosition::After)

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
                                            ->color('gray')
                                            ->label('Clinic'),


                                        TextEntry::make('appointment.date')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 2,
                                            ])
                                            ->color('gray')
                                            ->date()
                                            ->label('Date Schedule'),



                                        TextEntry::make('appointment.time')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 2,
                                            ])
                                            ->color('gray')
                                            ->date('H:i A')->timeZone('Asia/Manila')
                                            ->label('Time Schedule'),


                                        TextEntry::make('appointment.extra_pet_info')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 8,
                                            ])
                                            ->color('gray')
                                            ->markdown()
                                            ->label('Extra Details'),
                                // InfoSection::make('Schedule')


                                //     ->columns([
                                //         'sm' => 3,
                                //         'xl' => 6,
                                //         '2xl' => 8,
                                //     ])
                                //     ->schema([

                                        

                                //     ])->hidden(function ($record) {
                                //         if ($record->appointment) {
                                //             return false;
                                //         }
                                //         return true;
                                //     }),

                            ])->hidden(function ($record) {
                                if ($record->appointment) {
                                    return false;
                                }
                                return true;
                            }),

                        Tabs\Tab::make('Patient Details')

                            ->icon('heroicon-m-identification')
                            ->iconPosition(IconPosition::After)

                            ->schema([
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
                                InfoSection::make('Pet Profile')

                                    ->schema([

                                        ImageEntry::make('animal.image')
                                            ->height(300)
                                            ->disk('public')
                                            ->url(function ($state) {
                                                if ($state !== null) {
                                                    return Storage::url($state);
                                                }
                                                return null; // or an appropriate default URL or message
                                            })

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

                        Tabs\Tab::make('Examination Details')

                            ->icon('heroicon-m-folder-open')
                            ->iconPosition(IconPosition::After)
                            ->schema([
                                InfoSection::make('Services')
                                    ->schema([
                                        ViewEntry::make('services')->columnSpan([
                                            'sm' => 1,
                                            'xl' => 2,
                                            '2xl' => 8,
                                        ])
                                            ->label('Requested Services *')
                                            ->view('infolists.components.services-list'),
                                    ])->hidden(function (Patient $record) {
                                        if ($record->examination?->services) {
                                            return false;
                                        }
                                        return true;
                                    }),



                                InfoSection::make('Examination')
                                    ->schema([
                                        TextEntry::make('examination.exam_type')->color('gray')->label('Exam Type'),
                                        TextEntry::make('examination.examination_date')->color('gray')->label('Examine Date')->date(),
                                        TextEntry::make('examination.temperature')->color('gray')->label('Temperature'),
                                        TextEntry::make('examination.crt')->color('gray')->label('Cry'),
                                        TextEntry::make('examination.diagnosis')->color('gray')->label('Diagnosis')->columnSpanFull(),
                                        TextEntry::make('examination.exam_result')->color('gray')->label('Exam Result')->columnSpanFull(),
                                        ImageEntry::make('examination.image_result')
                                            ->disk('public')
                                            ->url(fn (Patient $patient) => $patient->examination->image_result ? Storage::disk('public')->url($patient->examination->image_result) : asset('/images/placeholder.png'))

                                            ->openUrlInNewTab()
                                            ->label('Image Result')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 8,
                                            ])
                                            ->defaultImageUrl(url('/images/placeholder.png')),


                                    ])->hidden(function (Patient $record) {
                                        if ($record->examination) {
                                            return false;
                                        }
                                        return true;
                                    }),

                                InfoSection::make('Examination Prescriptions')
                                    ->schema([

                                        // ViewEntry::make('examination.prescriptions')
                                        //     ->view('infolists.components.prescriptions-entry')->label('Prescriptions')->hidden(function (Patient $record) {
                                        //         if ($record->examination?->prescriptions) {
                                        //             return false;
                                        //         }
                                        //         return true;
                                        //     })

                                      
                                        RepeatableEntry::make('examination.prescriptions')
                                        ->columns([
                                            'sm' => 3,
                                            'xl' => 6,
                                            '2xl' => 6,
                                        ])
                                            ->schema([
                                                TextEntry::make('drug')
                                                ->columnSpan(2)
                                                ->color('gray')
                                                ->label('Prescription Drug')
                                                ,
                                                TextEntry::make('dosage')->columnSpan(2)
                                                ->color('gray')
                                                ->label('Prescription Dosage'),
                                                TextEntry::make('description')
                                                ->color('gray')  
                                                ->label('Prescription Description')
                                                ->columnSpan(2)

                                                   
                                                ])
                                                ->label('Prescriptions')
                                                ->columns(1),
                                                
                                        



                                    ])->hidden(function (Patient $record) {
                                        if ($record->examination) {
                                            return false;
                                        }
                                        return true;
                                    })

                                    ->collapsible()
                                ->collapsed()
                                ,

                                InfoSection::make('Examination Treatments')
                                    ->schema([
                                        RepeatableEntry::make('examination.treatments')
                                        ->columns([
                                            'sm' => 3,
                                            'xl' => 6,
                                            '2xl' => 6,
                                        ])
                                        ->schema([
                                         
    
                                                TextEntry::make('treatment')
                                                ->color('gray')
                                                    ->columnSpan(2),
                                                TextEntry::make('treatment_price')
                                                ->color('gray')
                                                ->columnSpan(2),
                                                TextEntry::make('treatment_date')
                                                ->date()
                                                ->color('gray')
                                                    ->columnSpan(2)
                                            
                                        ])
    
                                        ->label('Treatments')
                                        ->columns(1)

                                    ])
                                      ->collapsed()
                                    ->collapsible(),
                              

                                        ]),
                            Tabs\Tab::make('Admission Details')

                            ->icon('heroicon-m-computer-desktop')
                            ->iconPosition(IconPosition::After)

                            ->schema([

                               
                                TextEntry::make('admission.admission_date')->date()->label('Admission Date')
                                    ->color('gray')
                                    ->columnSpan(2)                                    ,
                                    TextEntry::make('admission.admission_time')->label('Admission Time')
                                    ->color('gray')
                                    ->date('H:i A')->timeZone('Asia/Manila')
                                    ->columnSpan(2),
                                    
                                    TextEntry::make('admission.status')->label('Admission Status')->color(function($state){
                                        return match($state){
                                            'Admitted' =>  'primary',
                                            'Discharged' =>  'success',
                                            default=> 'gray',
                                        };
                                    })
                                    ->columnSpan(2),


                                    InfoSection::make('Admission Treatment Plan & Monitoring')
                                    ->schema([
                                        RepeatableEntry::make('admission.treatmentplans')
                                        ->columns([
                                            'sm' => 3,
                                            'xl' => 6,
                                            '2xl' => 6,
                                        ])
                                        ->schema([
                                          
    
                                                TextEntry::make('drug')
                                                 ->label('Treatment Drug')
                                                ->color('gray')
                                                    ->columnSpan(2),
                                                TextEntry::make('dosage')
                                                ->label('Treatment Dosage')

                                                ->color('gray')
                                                ->columnSpan(2)
                                                ,
                                                TextEntry::make('date')
                                                ->label('Treatment Date')

                                                ->color('gray')    
                                                ->date()
                                                ->columnSpan(2),
                                                TextEntry::make('time')
                                                ->label('Treatment Time')

                                                ->date('H:i A')->timeZone('Asia/Manila')
                                                ->color('gray')    
                                                ->columnSpan(2),
                                                TextEntry::make('remarks')
                                                ->label('Treatment Remarks')

                                                ->color('gray')  
                                                ->columnSpanFull(),

                                                RepeatableEntry::make('monitors')
                                                ->columns([
                                                    'sm' => 3,
                                                    'xl' => 6,
                                                    '2xl' => 6,
                                                ])

                                                ->schema([
                                                    TextEntry::make('date')
                                                    ->label('Monitor Date')
                                                    ->color('gray')    
                                                    ->date()
                                                    ->columnSpan(2),

                                                    TextEntry::make('time')
                                                    ->label('Monitor Time')
    
                                                    ->date('H:i A')->timeZone('Asia/Manila')
                                                    ->color('gray')    
                                                    ->columnSpan(2),

                                                    TextEntry::make('details')
                                                    ->label('Monitor Details')
    
                                                    ->color('gray')  
                                                    ->columnSpanFull(),

                                                    TextEntry::make('observation')
                                                    ->label('Monitor Observation')
    
                                                    ->color('gray')  
                                                    ->columnSpanFull(),
                                                    TextEntry::make('remarks')
                                                    ->label('Monitor Remarks')
    
                                                    ->color('gray')  
                                                    ->columnSpanFull(),
                                                    ImageEntry::make('monitor_image')
                                            ->disk('public')
                                            ->url(fn ($state) => $state ? Storage::disk('public')->url($state) : asset('/images/placeholder.png'))

                                            ->openUrlInNewTab()
                                            ->label('Monitor Image')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 8,
                                            ])
                                            ->defaultImageUrl(url('/images/placeholder.png')),

                                                ]) 
                                                ->columnSpanFull()
                                                ->label('Monitors')
                                                ->columns(1),


                                        
                                        ])
                                        
                                       
    
                                        ->label('Treatments')
                                        ->columns(1)

                                    ])
                                      ->collapsed()
                                    ->collapsible(),
                              
                                


                            ])
                            ,

                            Tabs\Tab::make('Payment Details')

                            ->icon('heroicon-m-banknotes')
                            ->iconPosition(IconPosition::After)
                            ->schema([
                                RepeatableEntry::make('payments')
                                ->columns([
                                    'sm' => 3,
                                    'xl' => 6,
                                    '2xl' => 8,
                                ])
                                ->schema([
                                    TextEntry::make('title')
                                    ->label('Paytment Title')
                                    ->color('gray')  
                                    ->columnSpan(2),
                                    TextEntry::make('description')
                                    ->label('Paytment Description')
                                    ->color('gray')  
                                    ->columnSpan(2),
                                    TextEntry::make('amount')
                                    ->label('Paytment Ammount')
                                    ->color('gray')  
                                    ->money('PHP')
                                    ->columnSpan(2),

                                    ImageEntry::make('receipt_image')
                                    ->disk('public')
                                    ->height(60)
                                    ->url(fn ($state) => $state ? Storage::disk('public')->url($state) : asset('/images/placeholder.png'))

                                    ->openUrlInNewTab()
                                    ->label('Proof Of Payment ')
                                    ->columnSpan([
                                        'sm' => 1,
                                        'xl' => 2,
                                        '2xl' => 2,
                                    ])
                                    ->defaultImageUrl(url('/images/placeholder.png')),

                                ])
                                ->columnSpanFull()
                                ->label('Payments')
                                ]),

                    ])
                    ->activeTab(5)   
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
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }    
}
