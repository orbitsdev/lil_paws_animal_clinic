<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Clinic;
use App\Models\Patient;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Appointment;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AppointmentResource\Pages;
use Filament\Infolists\Components\Section as InfoSection;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;
use App\Filament\Resources\AppointmentResource\RelationManagers;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $modelLabel = 'Appointment Request';

    protected static ?string $navigationGroup = 'Management';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make()
                ->description('Select Your Clinic and Schedule
        ')->icon('heroicon-m-building-storefront')
                ->schema([
                    Select::make('clinic_id')
                        ->options(Clinic::query()->pluck('name', 'id'))
                        ->native(false)
                        ->label('What Clinic?')
                        ->required()
                        ->searchable()
                        ,
                        Select::make('veterinarian_id')
                        ->label('Select Veterenarian ')
                        ->relationship(
                            name: 'veterinarian',
                            modifyQueryUsing: fn (Builder $query, Get $get) =>  $query->whereHas('clinic', function ($query) use ($get) {
                                $query->where('id', $get('clinic_id'));
                            })
                        )

                        ->afterStateUpdated(function (?string $state, ?string $old, Get $get, Set $set) {
                            if (!empty($state)) {
                            }
                        })
                        ->required()
                        ->getOptionLabelFromRecordUsing(fn (Model $record) => $record?->first_name . ' ' . $record->last_name . ' - ' . $record->clinic->name)
                        ->live()
                        ->helperText(new HtmlString('Select veterinarian base on clinic that you selected else it will not be reflected'))


                        ->native(false)
                        ->preload()
                        ->searchable(),
                     

                    DatePicker::make('date')->required()->label('When?')
                    ->timezone('Asia/Manila')
                    ->minDate( now()->toDateString())
                    ->closeOnDateSelection()
                    ,
                    TimePicker::make('time')
                        ->timezone('Asia/Manila')
                        ->helperText(new HtmlString('(e.g., 02:30:00 PM)'))
                        ->required()
                        ->label('What Time?')
                        
                        ,

                    RichEditor::make('extra_pet_info')
                        ->toolbarButtons([
                            'blockquote',
                            'bold',
                            'bulletList',
                            'codeBlock',
                            'h2',
                            'h3',
                            'italic',
                            'link',
                            'orderedList',
                            'redo',
                            'strike',
                            'undo',
                        ])
                        ->label('Extra Pet Info (Optional 😊)')
                        ->helperText(new HtmlString('Add any extra details or notes about your appointment – it\'s your chance to shine! Whether it\'s your pet\'s condition, concerns, or special wishes, we\'re all ears. Let\'s make your visit paw-sitively purr-fect')),

                        Select::make('status')
                        ->label('Appointment Status') 
                        ->options([
                            'Accepted' => 'Accepted',
                            'Completed' => 'Completed',
                            'Pending' => 'Pending',
                            'Rejected' => 'Rejected',
                        ])
                        ->required()
                        ,
                       

                     ])->columnSpan(6),

                     
                            

                
            TableRepeater::make('patients')
            ->relationship()
            ->schema([
              
                Select::make('animal_id')
                ->label('Your Pet\'s Name')    
                ->relationship(
                        name: 'animal',
                        modifyQueryUsing: fn (Builder $query) => $query->whereHas('user')
                    )
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => ucfirst(optional($record)->name) .' - '. ucfirst(optional($record)->breed). ' - ( '. $record?->user?->first_name. ''. $record?->user?->last_name.')')
                    ->searchable(['animal.name', 'animal.breed'])
                    ->preload()
                    ->helperText(new HtmlString('Pet Name - Breed - (Owner) Make sure the ownder is the same'))
                    ->label('Pet Name')
                                      ,

                Select::make('services')
                ->label('Pick Services for Your Pet\'s Best ')    
                ->relationship(
                    name: 'services',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn (Builder $query, Get $get) => $query->when($get('animal_id'), function ($query) use ($get) {
                        $query->whereHas('categories.animals', function ($query) use ($get) {
                            $query->where('id', $get('animal_id'));
                        });
                    })
                     )
                     ->getOptionLabelFromRecordUsing(fn (Model $record) => optional($record)->name . ' - ₱' . number_format(optional($record)->cost))
                     ->helperText(new HtmlString('Select Services for Your Pet'))
                    ->multiple()
                    ->preload()
                    ->native(false)
                    ->searchable()
                   

            ])
            ->withoutHeader()
            ->hideLabels()
            ->hint('Let\'s Keep Things One of a Kind, Avoid duplication')
            ->label('Pets ')
            ->addActionLabel('Add Pet')
            ->columns(2)
            ->columnSpan(6)
            
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('patient.animal.user')
                    ->formatStateUsing(fn ($state): string => $state ? ucfirst($state->first_name . ' ' . $state->last_name) : '')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('patient.animal.user', function ($query) use ($search) {
                            $query->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('patient.animal.name')
                ->formatStateUsing(fn ($state): string => ucfirst($state))
                ->sortable()
                ->label('Pet Owner')
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query->whereHas('patient.animal', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
                })
                ,
                
                TextColumn::make('clinic.name')
                    ->formatStateUsing(fn (string $state): string => $state ? ucfirst($state) : $state)
                    ->sortable()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('clinic', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                    }),

                TextColumn::make('date')->date()->color('warning'),
                TextColumn::make('time')->date('h:i:s A'),
                TextColumn::make('patients.animal.name')
                    ->badge()
                    ->separator(',')
                    ->label('Patients')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('patients.animal', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                    }),


                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'primary',
                        'Accepted' => 'success',
                        'Completed' => 'success',
                        'Rejected' => 'danger',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Pending' => 'heroicon-o-ellipsis-horizontal-circle',
                        'Accepted' => 'heroicon-o-check-circle',
                        'Completed' => 'heroicon-s-check-circle',
                        'Rejected' => 'heroicon-o-x-mark',
                    })
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('clinic_id')
                    ->options(Clinic::query()->pluck('name', 'id'))->label('By Clinic'),
                SelectFilter::make('status')
                    ->options([
                        'Accepted' => 'Accepted',
                        'Completed' => 'Completed',
                        'Pending' => 'Pending',
                        'Rejected' => 'Rejected',
                    ])->label('By Status')

            ])
            ->actions([
                ActionGroup::make([

                    Tables\Actions\ViewAction::make()->color('primary')->label('View Details')->modalWidth('5xl'),
                    Tables\Actions\Action::make('update')
                        ->icon('heroicon-s-pencil-square')
                        ->label('Manage Request')
                        ->color('success')
                        ->fillForm(function (Appointment $record, array $data) {
                            return [
                                'status' => $record->status
                            ];
                        })
                        ->form([

                            Select::make('status')
                                ->label('Request Status')
                                ->options([
                                    'Accepted' => 'Accepted',
                                    'Pending' => 'Pending',
                                    // 'Completed' => 'Completed',
                                    'Rejected' => 'Reject',
                                ])
                                ->required(),
                            Select::make('clinic_id')
                                ->relationship(
                                    name: 'clinic',
                                    titleAttribute: 'name'
                                )
                                ->afterStateUpdated(function (?string $state, ?string $old, Get $get, Set $set) {
                                })

                                ->native(false)
                                ->preload()
                                ->required()
                                ->searchable(),
                            Select::make('veterinarian_id')
                                ->label('Select Veterenarian ')
                                ->relationship(
                                    name: 'veterinarian',
                                    modifyQueryUsing: fn (Builder $query, Get $get) =>  $query->whereHas('clinic', function ($query) use ($get) {
                                        $query->where('id', $get('clinic_id'));
                                    })
                                )

                                ->afterStateUpdated(function (?string $state, ?string $old, Get $get, Set $set) {
                                    if (!empty($state)) {
                                    }
                                })
                                ->required()
                                ->getOptionLabelFromRecordUsing(fn (Model $record) => $record?->first_name . ' ' . $record->last_name . ' - ' . $record->clinic->name)
                                ->live()
                                ->helperText(new HtmlString('Select veterinarian base on clinic that you selected else it will not be reflected'))


                                ->native(false)
                                ->preload()
                                ->searchable(),

                        ])
                        ->action(function (Appointment $record, array $data): void {
                            $l = [$data['clinic_id'], $data['veterenarian_id']];

                            $vetExist = User::where('id', (int)$data['veterenarian_id'])
                                ->whereHas('clinic', function ($query) use ($data) {
                                    $query->where('id', $data['clinic_id']);
                                })
                                ->first();

                           

                            if($vetExist){

                                $veterenarian_id =   match ($data['status']) {
                                    'Accepted' => (int)$data['veterenarian_id'],
                                    'Completed' => (int)$data['veterenarian_id'],
                                    'Pending' => null,
                                    'Rejected' => (int)$data['veterenarian_id'],
                                    default => null,
                                };
    
                                $clinic_id =   match ($data['status']) {
                                    'Accepted' => $data['clinic_id'],
                                    'Completed' => $data['clinic_id'],
                                    'Pending' => null,
                                    'Rejected' => $data['clinic_id'],
                                    default => null,
                                };
    
                                $record->veterinarian_id = $veterenarian_id;
                                $record->status = $data['status'];
    
                                $patients = Patient::where('appointment_id', $record->id)->get();
    
    
                                foreach ($patients as $patient) {
                                    $patient->clinic_id = $clinic_id; // Set the clinic_id from the appointment
                                    $patient->save();
                                }
                                $record->save();

                            }else{

                                return;
                            }






                           
                        }),
                    
                 
                    Tables\Actions\EditAction::make()->color('info')->label('Edit Appointment'),
                    Tables\Actions\DeleteAction::make(),

                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Appointment Information')
                    ->tabs([
                        Tabs\Tab::make('Overview')
                            ->icon('heroicon-m-calendar')
                            ->schema([
                                InfoSection::make('Clinic & Schedule')


                                    ->columns([
                                        'sm' => 3,
                                        'xl' => 6,
                                        '2xl' => 8,
                                    ])
                                    ->schema([

                                        TextEntry::make('status')
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


                                        TextEntry::make('clinic.name')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 2,
                                            ])
                                            ->color('gray')
                                            ->label('Appointment Clinic'),


                                        TextEntry::make('date')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 2,
                                            ])
                                            ->date()
                                            ->color('gray')
                                            ->label('Appointment Date '),



                                        TextEntry::make('time')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 2,
                                            ])

                                            ->date('H:i:s A')->timeZone('Asia/Manila')
                                            ->color('gray')
                                            ->label('Appointment Time '),


                                        TextEntry::make('extra_pet_info')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 8,
                                            ])

                                            ->markdown()
                                            ->label('Appointment Extra Details'),

                                    ]),
                                InfoSection::make('Pet Owner ')


                                    ->columns([
                                        'sm' => 3,
                                        'xl' => 6,
                                        '2xl' => 8,
                                    ])
                                    ->schema([
                                        TextEntry::make('patient.animal.user')->columnSpan(6)->label('Owner')
                                        ->formatStateUsing(fn ($record)=> $record->patient?->animal?->user?->first_name. ' '.$record->patient?->animal?->user?->last_name)
                                            ->label('Name')
                                            ->color('gray')
                                            ->columnSpan([
                                                'sm' => 2,
                                                'xl' => 3,
                                                '2xl' => 4,
                                            ]),


                                        TextEntry::make('patient.animal.user')->columnSpan(6)->label('Phone Number')
                                            ->formatStateUsing(fn ($record): string => !empty($record->patient?->animal?->user?->phone_number) ? $record->patient?->animal?->user?->phone_number : 'N/S')
                                            ->color('gray')
                                            ->columnSpan([
                                                'sm' => 2,
                                                'xl' => 3,
                                                '2xl' => 4,
                                            ]),
                                        TextEntry::make('patient.animal.user')->columnSpan(6)->label('Address')
                                            ->formatStateUsing(fn ($record): string => !empty($record->patient?->animal?->user?->address) ? $record->patient?->animal?->user?->address : 'N/S')
                                            ->color('gray')
                                            ->columnSpan([
                                                'sm' => 2,
                                                'xl' => 3,
                                                '2xl' => 4,
                                            ]),
                                        TextEntry::make('patient.animal.user.email')->columnSpan(6)->label('Email')
                                            ->color('gray')
                                            ->columnSpan([
                                                'sm' => 2,
                                                'xl' => 3,
                                                '2xl' => 4,
                                            ]),

                                    ]),
                                InfoSection::make('Pets')

                                    ->schema([

                                        RepeatableEntry::make('patients')
                                            ->schema([

                                                ImageEntry::make('animal.image')
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
                                                    ->color('gray')
                                                    ->columnSpan([
                                                        'sm' => 1,
                                                        'xl' => 2,
                                                        '2xl' => 2,
                                                    ]),
                                                TextEntry::make('animal.breed')
                                                    ->label('Breed')
                                                    ->color('gray')
                                                    ->columnSpan([
                                                        'sm' => 1,
                                                        'xl' => 2,
                                                        '2xl' => 2,
                                                    ]),
                                                TextEntry::make('animal.sex')
                                                    ->label('Sex')
                                                    ->color('gray')
                                                    ->columnSpan([
                                                        'sm' => 1,
                                                        'xl' => 2,
                                                        '2xl' => 2,
                                                    ]),
                                                TextEntry::make('animal.date_of_birth')
                                                    ->date()
                                                    ->hintIcon('heroicon-m-calendar-days')
                                                    ->label('Birth date')
                                                    ->color('gray')
                                                    ->columnSpan([
                                                        'sm' => 1,
                                                        'xl' => 2,
                                                        '2xl' => 2,
                                                    ]),

                                                TextEntry::make('animal.weight')
                                                    ->label('Weight')
                                                    ->color('gray')
                                                    ->columnSpan([
                                                        'sm' => 1,
                                                        'xl' => 2,
                                                        '2xl' => 2,
                                                    ]),





                                            ])

                                            ->label('Pets Name')
                                            ->contained(false)
                                            ->columnSpan(6),
                                    ]),
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
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
