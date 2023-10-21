<?php

namespace App\Filament\Clinic\Resources;

use Tabs\Tab;
use Filament\Forms;
use Filament\Tables;
use App\Models\Clinic;
use App\Models\Patient;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Appointment;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\Tabs;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Infolists\Components\ImageEntry;
use Filament\Forms\Components\Tabs as FormTabs;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Tabs\Tab as FormChildTab;
use Filament\Infolists\Components\Section as InfoSection;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;
use App\Filament\Clinic\Resources\AppointmentResource\Pages;
use App\Filament\Clinic\Resources\AppointmentResource\RelationManagers;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $modelLabel = 'Appointments Request';
    protected static ?string $navigationGroup = 'Management';
    protected static ?int $navigationSort = 6;


    public static function getNavigationBadge(): ?string
{
    return static::getModel()::where('clinic_id', auth()->user()->clinic?->id)->where('status','!=','Accepted')->count();

}
    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Appointment Details')
                    ->schema([

                        Group::make()
                            ->relationship('clinic')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Clinic')
                                    ->required()
                                    ->disabled()
                                    ->columnSpan([
                                        'sm' => 2,
                                        'xl' => 3,
                                        '2xl' => 4,
                                    ])->label('Clinic'),
                            ]),
                        DatePicker::make('date')->required()->label('Schedule Date')
                            ->timezone('Asia/Manila')
                            ->closeOnDateSelection()
                            ->displayFormat('d/m/Y')
                            ->disabled(),

                        TimePicker::make('time')
                            ->timezone('Asia/Manila')
                            ->required()
                            ->label('Scheduled Time')
                            ->disabled(),

                        RichEditor::make('extra_pet_info')
                            ->toolbarButtons([])
                            ->label('Extra Pet Info')
                            ->disabled(),
                        Group::make()
                            ->relationship('patient')
                            ->schema([

                                Select::make('services')
                                    ->label('Selected Services')
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
                                    ->multiple()
                                    ->disabled()
                                    ->preload()
                                    ->native(false),
                            ]),
                    ]),

                Section::make('Pet Health Records')
                    ->description('Record and manage pet examinations and prescriptions')
                    ->schema([
                        Repeater::make('patients')
                            ->relationship()
                            ->schema([
                                Group::make()
                                    ->relationship('animal')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Name')
                                            ->required()
                                            ->disabled(),
                                    ]),




                                Repeater::make('examinations')
                                    ->relationship()
                                    ->label('Examination')
                                    ->schema([
                                        Grid::make(10)->schema([
                                            TextInput::make('exam_type')
                                                ->label('Examination Type')
                                                ->columnSpan(2),



                                            TextInput::make('temperature')
                                                ->columnSpan(2),

                                            TextInput::make('crt')
                                                ->columnSpan(2),


                                            TextInput::make('price')
                                                ->numeric()
                                                ->prefix('$')
                                                ->columnSpan(2),


                                            DatePicker::make('examination_date')
                                                ->columnSpan(2),

                                            TextArea::make('exam_result')
                                                ->columnSpanFull()
                                                ->columnSpan(5)
                                                ->rows(5),




                                            TextArea::make('diagnosis')
                                                ->columnSpan(5)
                                                ->rows(5),
                                        ]),





                                        FileUpload::make('image_result')
                                            ->disk('public')->image()->directory('examination-ressult')
                                            ->columnSpanFull(),

                                        TableRepeater::make('prescriptions')
                                            ->relationship()
                                            ->schema([
                                                TextInput::make('drug'),
                                                TextInput::make('dosage'),
                                                TextInput::make('description'),
                                            ])

                                            ->columnSpanFull()
                                            ->withoutHeader()


                                    ])
                                    ->collapsible()

                                    ->maxItems(1),

                                                


                                             

                            ])

                            ->columnSpanFull()
                            ->addable(false)
                            ->deletable(false)
                            ->label('Pets'),
                    ]),



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
                    ->options(Clinic::query()->pluck('name', 'id'))->label('By Clinic')
                    ->default(fn () => auth()->user()->clinic?->id)
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()->color('primary')->label('View Details')->modalWidth('7xl'),
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

                        ])
                        ->action(function (Appointment $record, array $data): void {


                            $veterenarian_id =   match ($data['status']) {
                                'Accepted' => auth()->user()->id,
                                'Completed' => auth()->user()->id,
                                'Pending' => null,
                                'Rejected' => auth()->user()->id,
                                default => null,
                            };

                            $clinic_id =   match ($data['status']) {
                                'Accepted' => auth()->user()->clinic?->id,
                                'Completed' => auth()->user()->clinic?->id,
                                'Pending' => null,
                                'Rejected' => auth()->user()->clinic?->id,
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


        
                        })->hidden(function ($record) {
                            return match ($record->status) {
                                'Accepted' => auth()->user()->id == $record?->veterinarian?->id ? false : true,
                                'Completed' => auth()->user()->id == $record?->veterinarian?->id ? false : true,
                                'Rejected' => auth()->user()->id == $record?->veterinarian?->id ? false : true,
                                default => false,
                            };
                        }),
                    // Tables\Actions\EditAction::make('manage-prescription')->label('Manage Exam & Rx')
                    //     ->icon('heroicon-s-pencil')
                    //     ->color('success')
                    //     ->tooltip('dsad')
                    //     ->hidden(function ($record) {
                    //         if ($record->hasStatus(['Accepted', 'Completed']) && (auth()->user()->id == $record->veterinarian?->id)) {
                    //             return false;
                    //         }

                    //         return true;
                    //     }),
                  
                    // Tables\Actions\DeleteAction::make(),
                ])->tooltip('Manage Appointment'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])
                    ->label('Delete Records'),
            ])
            ->emptyStateActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->where('clinic_id', auth()->user()->clinic?->id))
            ->poll('5s');
            
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
                                        TextEntry::make('user')->columnSpan(6)->label('Owner')
                                            ->formatStateUsing(fn (Appointment $record): string => ucfirst($record->user->first_name) . ' ' . ucfirst($record->user->last_name))
                                            ->label('Name')
                                            ->color('gray')
                                            ->columnSpan([
                                                'sm' => 2,
                                                'xl' => 3,
                                                '2xl' => 4,
                                            ]),


                                        TextEntry::make('user')->columnSpan(6)->label('Phone Number')
                                            ->formatStateUsing(fn (Appointment $record): string => !empty($record->user->phone_number) ? $record->user->phone_number : 'N/S')
                                            ->color('gray')
                                            ->columnSpan([
                                                'sm' => 2,
                                                'xl' => 3,
                                                '2xl' => 4,
                                            ]),
                                        TextEntry::make('user')->columnSpan(6)->label('Address')
                                            ->formatStateUsing(fn (Appointment $record): string => !empty($record->user->address) ? $record->user->address : 'N/S')
                                            ->color('gray')
                                            ->columnSpan([
                                                'sm' => 2,
                                                'xl' => 3,
                                                '2xl' => 4,
                                            ]),
                                        TextEntry::make('user.email')->columnSpan(6)->label('Email')
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
