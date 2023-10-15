<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\Tabs;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\Resources\PatientResource\Pages;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Section as InfoSection;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;
use App\Filament\Resources\PatientResource\RelationManagers;


class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-plus';

    protected static ?string $modelLabel = 'Medical Record';
    protected static ?string $navigationGroup = 'Management';
    protected static ?int $navigationSort = 3;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Patient Information')
                ->description('Please provide the essential details for creating a new patient record. This information is crucial for managing your patients effectively.')


                ->schema([


                    Select::make('animal_id')
                        ->relationship(
                            name: 'animal',
                            titleAttribute: 'name',
                        )
                        ->label('Pets Name')
                        ->getOptionLabelFromRecordUsing(function (Model $record) {
                            // Get the animal name and capitalize the first letter
                            $animalName = ucfirst(optional($record)->name);

                            // Get the category name from the related category and capitalize the first letter
                            $categoryName = ucfirst(optional($record->category)->name);

                            // Get the user's first name and capitalize the first letter
                            $firstName = ucfirst(optional($record->user)->first_name);

                            // Get the user's last name and capitalize the first letter
                            $lastName = ucfirst(optional($record->user)->last_name);

                            // Create the final label by concatenating the parts
                            return "$animalName - $categoryName - $firstName $lastName";
                        })

                        ->preload()
                        ->required()
                        ->searchable()
                        ->helperText(new HtmlString('<small>Pet Name - Breed - Pet Owner</small'))
                        ->createOptionForm([
                            Section::make()
                                ->description('Pet Profile ')->schema([
                                    Select::make('user_id')
                                        ->relationship(
                                            name: 'user',
                                            titleAttribute: 'first_name',
                                            modifyQueryUsing: fn (Builder $query) => $query->whereHas('role', function ($query) {
                                                $query->where('name', 'Client');
                                            }),
                                        )

                                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->first_name} {$record->last_name} ")
                                        ->preload()
                                        ->required()
                                        ->label('Pet Owner')
                                        ->searchable()
                                        ->native(false)
                                        ->createOptionForm([
                                            FileUpload::make('profile')
                                                ->disk('public')
                                                ->directory('user-profile')
                                                ->image()
                                                ->imageEditor()
                                                ->imageEditorAspectRatios([
                                                    '16:9',
                                                    '4:3',
                                                    '1:1',
                                                ])
                                                // ->imageEditorMode(2)
                                                ->columnSpan(2)
                                                ->required(),
                                            Section::make()
                                                ->description('This will be display as user account')
                                                ->schema([
                                                    TextInput::make('first_name')->required(),
                                                    TextInput::make('last_name')->required(),
                                                    TextInput::make('phone_number')->required()->numeric(),
                                                    TextInput::make('address')->required(),
                                                    TextInput::make('email')->required()->unique(ignoreRecord: true),

    
                                                    TextInput::make('password')
                                                        ->label(fn (string $operation) => $operation == 'create' ? 'Password' : 'New Password')
                                                        ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                                                        ->dehydrated(fn (?string $state): bool => filled($state))
                                                        ->required(fn (string $operation): bool => $operation === 'create'),
                                                ]),

                                        ])
                                ]),

                             

                            Section::make()
                                ->description('Pet Profile ')
                                ->icon('heroicon-m-sparkles')


                                ->schema([
                                    TextInput::make('name')->required()->label('Pet Name'),
                                    Select::make('category_id')
                                        ->options(Category::pluck('name', 'id'))
                                        ->required()
                                        ->label('Pet Category')
                                        ->native(false)
                                        ->searchable(),
                                    TextInput::make('breed')->required()->label('Pet Breed'),
                                    Select::make('sex')->options([
                                        'Male' => 'Male',
                                        'Female' => 'Female',
                                    ])->required(),
                                    DatePicker::make('date_of_birth'),
                                    TextInput::make('weight')->required()->label('Weight'),

                                    FileUpload::make('image')
                                        ->disk('public')
                                        ->directory('animal-profile')
                                        ->image()
                                        ->imageEditor()
                                        ->imageEditorMode(2)
                                        ->required()

                                ]),
                            ]),

                            Select::make('clinic_id')
                            ->relationship(
                                name: 'clinic',
                                titleAttribute: 'name',
                            )
                            ->label('Clinic Name')
                            ->preload()
                            ->required()
                            ->searchable()
                            ,



                ])
                ->collapsible()
            // ->collapsed()
            ,
            Section::make('Patient Health Records')
                ->description('Record and manage pet examinations and prescriptions')
                ->schema([
                    Repeater::make('examinations')
                        ->relationship()
                        ->label('Examination')
                        ->schema([
                            Grid::make(10)->schema([
                                TextInput::make('exam_type')
                                    ->label('Examination Type')
                                    ->columnSpan(4),



                                TextInput::make('temperature')
                                    ->label('Examination Temperature')
                                    ->columnSpan(2),

                                TextInput::make('crt')
                                    ->label('Examination CRT')
                                    ->columnSpan(2),


                                TextInput::make('price')
                                    ->label('Examination Price')
                                    ->numeric()
                                    ->prefix('$')
                                    ->columnSpan(2)->hidden(),


                                DatePicker::make('examination_date')
                                    ->columnSpan(2),

                                TextArea::make('exam_result')
                                    ->label('Examination Result')
                                    ->columnSpanFull()
                                    ->columnSpan(10)
                                    ->rows(2),




                                TextArea::make('diagnosis')
                                    ->label('Examination Diagnosis')
                                    ->columnSpan(10)
                                    ->rows(2),
                            ]),





                            FileUpload::make('image_result')
                                ->disk('public')->image()->directory('examination-ressult')
                                ->columnSpanFull()
                                ->label('Examination Image Result'),

                            TableRepeater::make('prescriptions')
                                ->relationship()
                                ->schema([
                                    TextInput::make('drug')
                                        ->label('Prescription Drug'),
                                    TextInput::make('dosage')
                                        ->label('Prescription Dosage'),
                                    TextInput::make('description')
                                        ->label('Prescription Description'),
                                ])
                                ->addActionLabel('Add Prescription')
                                ->columnSpanFull()
                                ->withoutHeader()
                                ->defaultItems(0)
                                ->collapsible()
                                ->collapsed(),


                            TableRepeater::make('treatments')
                                ->relationship()
                                ->label('Examination Treatments')
                                ->schema([
                                    TextInput::make('treatment'),
                                    TextInput::make('treatment_price')->numeric(),
                                    DatePicker::make('treatment_date')

                                ])
                                ->columnSpanFull()
                                ->withoutHeader()
                                ->collapsible()
                                ->collapsed()
                                ->addActionLabel('Add Treatment')
                                ->defaultItems(0),
                        ])
                        ->addActionLabel('Add Examination')
                        ->defaultItems(0)
                        ->collapsible()

                        ->maxItems(1),
                ])

                ->collapsible()
                ->collapsed(),

            Section::make('Admissions , Treatments Plan & Monitoring')
                ->description('Record and manage pet examinations and prescriptions')
                ->schema([
                    Repeater::make('admissions')
                        ->relationship()
                        ->label('Admission')
                        ->schema([
                            Grid::make(8)->schema([

                                Select::make('veterinarian_id')
                                    ->relationship(
                                        name: 'veterenarian',
                                        titleAttribute: 'first_name',
                                        modifyQueryUsing: fn (Builder $query) => $query->whereHas('role', function ($query) {
                                            $query->where('name', 'Veterenarian');
                                        })->whereHas('clinic', function ($query) {
                                            $query->where('id', auth()->user()->clinic?->id);
                                        })
                                    )
                                    ->label('Admission Veterenarian')
                                    ->columnSpan(2),

                                DatePicker::make('admission_date')
                                    ->columnSpan(2),

                                TimePicker::make('admission_time')
                                    ->timezone('Asia/Manila')
                                    ->columnSpan(2),

                                Select::make('status')
                                    ->options([
                                        'Admitted' => 'Admitted',
                                        'Discharged' => 'Discharged',

                                    ])->label('Admission Status')
                                   
                                    ->columnSpan(2),

                                    ImageEntry::make('veterenarian.profile')
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

                                       



                            ]),

                            Repeater::make('treatmentplans')
                                ->relationship()
                                ->schema([

                                    Grid::make(8)->schema([
                                        TextInput::make('drug')
                                            ->columnSpan(2)
                                            ->label('Treatment Plan Drug'),
                                        TextInput::make('dosage')
                                            ->label('Treatment Plan dosage')
                                            ->columnSpan(2),
                                        DatePicker::make('date')
                                            ->columnSpan(2)
                                            ->label('Treatment Plan Date'),
                                        TimePicker::make('time')
                                            ->label('Treatment Plan Time')
                                            ->columnSpan(2)
                                            ->timezone('Asia/Manila'),
                                        TextArea::make('remarks')
                                            ->label('Treatment Plan Remarks')
                                            ->columnSpanFull(),
                                    ])->columnSpanFull(),


                                    Repeater::make('monitors')
                                        ->relationship()
                                        ->label('Monitoring Record')
                                        ->schema([
                                            Grid::make(8)->schema([
                                                DatePicker::make('date')
                                                    ->label('Monitoring Date')
                                                    ->columnSpan(2),
                                                TimePicker::make('time')
                                                    ->label('Monitoring Time')
                                                    ->columnSpan(2),
                                                TextInput::make('activity')
                                                    ->label('Monitoring Activity')
                                                    ->columnSpan(2),
                                                TextInput::make('details')
                                                    ->label('Monitoring Details')
                                                    ->columnSpan(2),
                                                TextArea::make('observation')
                                                    ->label('Monitoring Observation')
                                                    ->columnSpan(4)
                                                    ->rows(2),

                                                TextArea::make('remarks')
                                                    ->columnSpanFull()
                                                    ->columnSpan(4)
                                                    ->label('Monitoring Remarks')
                                                    ->rows(2),

                                                FileUpload::make('monitor_image')
                                                    ->disk('public')->image()->directory('monitoring-image')
                                                    ->columnSpanFull()
                                                    ->label('Monitoring Image'),

                                            ]),
                                        ])
                                        // ->withoutHeader()
                                        ->defaultItems(0)
                                        ->addActionLabel('Add Monitoring')
                                        ->collapsible()
                                        ->columnSpanFull()


                                ])
                                ->label('Admission Treatment Plans')
                                ->addActionLabel('Add Treatment Plan')
                                ->columnSpanFull()
                                // ->withoutHeader()
                                ->defaultItems(0)
                                ->collapsible()



                        ])
                        ->defaultItems(0)
                        ->collapsible()
                        ->addActionLabel('Add Admission')
                        ->maxItems(1),
                ])
                ->collapsible()
                ->collapsed(),


            // Section::make('Payments Information')
            //     ->description('Keep track of  payments easily. you can add report payment details here. (If you had)')
            //     ->schema([

            //         TableRepeater::make('payments')
            //             ->relationship()
            //             ->label('List')
            //             ->columnWidths([
            //                 'receipt_image' => '300px',
            //             ])
            //             ->schema([
            //                 TextInput::make('title'),
            //                 TextInput::make('description'),
            //                 TextInput::make('amount')->numeric()->prefix('₱'),
            //                 FileUpload::make('receipt_image')
            //                     ->disk('public')->image()->directory('receipt')
            //                     ->columnSpanFull()
            //                     ->label('Proof of payment'),
            //             ])
            //             ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
            //                 $data['clinic_id'] = auth()->user()->clinic?->id;

            //                 return $data;
            //             })
            //             ->addActionLabel('Add Payment Information')
            //             // ->hideLabels()
            //             ->defaultItems(0)
            //             ->collapsible()
            //             ->collapsed()
            //             ->columnSpanFull()
            //             ->withoutHeader(),
            //     ])
            //     ->collapsible(true)
            //     ->collapsed()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('clinic.name')->label('Clinic')->badge('primary'),
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
           
                TextColumn::make('animal')->label('Owner')->formatStateUsing(function (Patient $record) {
                    return ucfirst($record->animal?->user?->first_name . ' ' . $record->animal?->user?->last_name);
                })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('animal.user', function ($query) use ($search) {
                            $query->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    }),

              
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
                    Tables\Actions\EditAction::make()->color('info'),
                    Tables\Actions\DeleteAction::make()
                ]),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->groups([
                'clinic.name',
            ]);
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

                            // Tabs\Tab::make('Payment Details')

                            // ->icon('heroicon-m-banknotes')
                            // ->iconPosition(IconPosition::After)
                            // ->schema([
                            //     RepeatableEntry::make('payments')
                            //     ->columns([
                            //         'sm' => 3,
                            //         'xl' => 6,
                            //         '2xl' => 8,
                            //     ])
                            //     ->schema([
                            //         TextEntry::make('title')
                            //         ->label('Paytment Title')
                            //         ->color('gray')  
                            //         ->columnSpan(2),
                            //         TextEntry::make('description')
                            //         ->label('Paytment Description')
                            //         ->color('gray')  
                            //         ->columnSpan(2),
                            //         TextEntry::make('amount')
                            //         ->label('Paytment Ammount')
                            //         ->color('gray')  
                            //         ->money('PHP')
                            //         ->columnSpan(2),

                            //         ImageEntry::make('receipt_image')
                            //         ->disk('public')
                            //         ->height(60)
                            //         ->url(fn ($state) => $state ? Storage::disk('public')->url($state) : asset('/images/placeholder.png'))

                            //         ->openUrlInNewTab()
                            //         ->label('Proof Of Payment ')
                            //         ->columnSpan([
                            //             'sm' => 1,
                            //             'xl' => 2,
                            //             '2xl' => 2,
                            //         ])
                            //         ->defaultImageUrl(url('/images/placeholder.png')),

                            //     ])
                            //     ->columnSpanFull()
                            //     ->label('Payments')
                            //     ]),

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
