<?php

namespace App\Filament\Clinic\Resources;

use Filament\Forms;
use App\Models\Role;
use App\Models\User;
use Filament\Tables;
use App\Models\Animal;
use App\Models\Clinic;
use Livewire\Livewire;
use App\Models\Patient;
use Filament\Forms\Get;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Appointment;
use App\Models\Examination;
use Illuminate\Support\Carbon;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Group;
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
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Tables\Grouping\Group as GroupBy;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Section as InfoSection;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;
use App\Filament\Clinic\Resources\ExaminationResource\Pages;
use App\Filament\Clinic\Resources\ExaminationResource\RelationManagers;
use App\Filament\Clinic\Resources\ExaminationResource\Pages\EditExamination;

class ExaminationResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-plus';
    protected static ?string $modelLabel = 'Pet Record';

    //     protected function getTableQuery(): Builder
    // {
    //     Patient::select('patients.*')
    //     ->join('animals', 'patients.animal_id', '=', 'animals.id')
    //     ->groupBy('animals.user_id')
    //     ->get();
    // }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Patient Information')
                ->description('Please provide the essential details for creating a new patient record. This information is crucial for managing your patients effectively.')
            

                    ->schema([
                        
                        // Select::make('animal.user_id')
                        // ->options(User::whereHas('role', function($query){
                        //     $query->where('name', 'Client');
                        // })->pluck('first_name', 'id'))
                        // ->label('Pet Owner')
                        // ->searchable()
                        // ->native(false)
                        // ->required(),

                        // Select::make('animal_id')
                        // ->label("Pet's Name")
                        // ->relationship('animal', 'id',
                        // modifyQueryUsing: function (Builder $query, Get $get) {
                        //     return $query->whereHas('user', function ($query) use ($get) {
                        //         $query->where('id', $get('animal.user_id'));
                        //     });
                        // }
                        // )                        
                        // ->preload()
                        // ->native(false)
                        // ->searchable()
                       
                        // ->getOptionLabelFromRecordUsing(function (Model $record) {
                        //     return ucfirst(optional($record)->name) . ' - ' . ucfirst(optional($record)->breed);
                        // })

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
                        ->createOptionForm([
                            Section::make()
                            ->description('Pet Profile ')->schema([
                                Select::make('user_id')
                                ->relationship(
                                    name: 'user',
                                    titleAttribute: 'first_name',
                                    modifyQueryUsing: fn (Builder $query) => $query->whereHas('role', function($query){
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
                                    ->required()
                                    ,
                                    Section::make()
                                    ->description('This will be display as user account')
                                    ->schema([
                                        TextInput::make('first_name')->required(),
                                        TextInput::make('last_name')->required(),
                                        TextInput::make('phone_number')->required()->numeric(),
                                        TextInput::make('address')->required(),
                                        TextInput::make('email')->required()->unique(ignoreRecord: true),

                                        // Select::make('role_id')
                                        // ->required()
                                        // ->label('Role')
                                        // ->options(Role::all()->pluck('name', 'id'))
                                        // ->searchable()
                                        // ->live()
                                        // ,
                    
                                        // Select::make('clinic_id')
                                        // ->required()
                                        // ->label('Clinic')
                                        // ->options(Clinic::all()->pluck('name', 'id'))
                                        // ->searchable()
                                      
                                        // ->hidden(function(Get $get){
                                        //     $role = Role::find($get('role_id'));
                                        //     if(!empty($role)){
                                        //         return $role->name != 'Veterenarian';
                                        //     }
                                        // })
                                        // ,
                                        
                                        
                                        TextInput::make('password')
                                        ->label(fn (string $operation) => $operation =='create' ? 'Password' : 'New Password')
                                        ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                                        ->dehydrated(fn (?string $state): bool => filled($state))
                                        ->required(fn (string $operation): bool => $operation === 'create')
                                        
                                        ,
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
                                ->searchable()
                                ,
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
                        ])
                      
                        

                    ])
                    ->collapsible()
                    ->collapsed()
                    ,
                // Section::make('Appointment Details')
                //     ->schema([
                //         Group::make()
                //             ->relationship('clinic')
                //             ->schema([
                //                 TextInput::make('name')
                //                     ->label('Clinic')
                //                     ->required()
                //                     ->disabled()
                //                     ->columnSpan([
                //                         'sm' => 2,
                //                         'xl' => 3,
                //                         '2xl' => 4,
                //                     ])->label('Clinic'),


                //             ]),
                //         Group::make()
                //             ->relationship('appointment')
                //             ->schema([
                //                 DatePicker::make('date')->required()->label('Schedule Date')
                //                     ->timezone('Asia/Manila')
                //                     ->closeOnDateSelection()
                //                     ->displayFormat('d/m/Y')
                //                     ->disabled(),

                //                 TimePicker::make('time')
                //                     ->timezone('Asia/Manila')
                //                     ->required()
                //                     ->label('Scheduled Time')
                //                     ->disabled(),

                //                 RichEditor::make('extra_pet_info')
                //                     ->toolbarButtons([])
                //                     ->label('Extra Pet Info')
                //                     ->disabled(),


                //             ]),

                //         Select::make('services')
                //             ->label('Selected Services')
                //             ->relationship(
                //                 name: 'services',
                //                 titleAttribute: 'name',
                //                 modifyQueryUsing: fn (Builder $query, Get $get) => $query->when($get('animal_id'), function ($query) use ($get) {
                //                     $query->whereHas('categories.animals', function ($query) use ($get) {
                //                         $query->where('id', $get('animal_id'));
                //                     });
                //                 })
                //             )
                //             ->getOptionLabelFromRecordUsing(fn (Model $record) => optional($record)->name . ' - ₱' . number_format(optional($record)->cost))
                //             ->multiple()
                //             ->disabled()
                //             ->preload()
                //             ->native(false),






                //     ])

                //     ->collapsed(true)
                //     ->hidden(fn (string $operation): bool => $operation === 'create'),


                Section::make('Pet Health Records')
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
                                        ->columnSpan(2),

                                    TextInput::make('crt')
                                        ->columnSpan(2),


                                    TextInput::make('price')
                                        ->numeric()
                                        ->prefix('$')
                                        ->columnSpan(2)->hidden(),


                                    DatePicker::make('examination_date')
                                        ->columnSpan(2),

                                    TextArea::make('exam_result')
                                        ->columnSpanFull()
                                        ->columnSpan(10)
                                        ->rows(5),




                                    TextArea::make('diagnosis')
                                        ->columnSpan(10)
                                        ->rows(5),
                                ]),





                                FileUpload::make('image_result')
                                    ->disk('public')->image()->directory('examination-ressult')
                                    ->columnSpanFull()
                                    ->label('Image Result'),

                                TableRepeater::make('prescriptions')
                                    ->relationship()
                                    ->schema([
                                        TextInput::make('drug'),
                                        TextInput::make('dosage'),
                                        TextInput::make('description'),
                                    ])
                                    ->addActionLabel('Add To Prescriptions')
                                    ->columnSpanFull()
                                    ->withoutHeader()
                                    ->defaultItems(0)
                                    ->collapsible()
                                    ->collapsed()
                            ])
                            ->addActionLabel('Add Examination')
                            ->defaultItems(0)
                            ->collapsible()
                            ->collapsed()
                            ->maxItems(1),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ,

                    Section::make('Payments Information')
                    ->description('Keep track of  payments easily. you can add report payment details here. (If you had)')
                    ->schema([
                        
                        TableRepeater::make('payments')
                        ->relationship()
                        ->label('List')
                        ->columnWidths([
                            'receipt_image' => '300px',
                        ])
                        ->schema([
                            TextInput::make('title'),
                            TextInput::make('description'),
                            TextInput::make('amount')->numeric()->prefix('₱'),
                            FileUpload::make('receipt_image')
                            ->disk('public')->image()->directory('receipt')
                            ->columnSpanFull()
                            ->label('Proof of payment'),
                        ])
                        ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                            $data['clinic_id'] = auth()->user()->clinic?->id;
                     
                            return $data;
                        })
                        ->addActionLabel('Add Payment Information')
                        // ->hideLabels()
                        ->collapsible()
                        ->collapsed()
                        ->columnSpanFull()
                        ->withoutHeader(),
                    ])
                    ->collapsible(true)
                    ->collapsed()


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
                // TernaryFilter::make('appointment')
                // ->nullable()
            ])
            ->actions([
                ActionGroup::make([


                    Tables\Actions\EditAction::make('manage-prescription')->label('Manage Exam & Rx')
                        ->icon('heroicon-s-pencil')
                        ->color('success')
                        ->tooltip('dsad'),
                    // Tables\Actions\Action::make('view record')->url(fn ($record): string => self::getUrl('record', ['record' => $record])),
                    Tables\Actions\ViewAction::make()->color('primary'),
                    Tables\Actions\DeleteAction::make(),
                ])->tooltip('Manage Appointment'),

            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->where('clinic_id', auth()->user()->clinic?->id))
            // ->groups([
            //     GroupBy::make('animal.name')
            //         ->label('Pet ')
            //         ->collapsible(),
            // ])
            // ->groupsInDropdownOnDesktop()
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
                        Tabs\Tab::make('Services , Examination & Prescriptions')

                            ->icon('heroicon-m-sparkles')
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
                                    ]),



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
            'index' => Pages\ListExaminations::route('/'),
            'create' => Pages\CreateExamination::route('/create'),
            'edit' => Pages\EditExamination::route('/{record}/edit'),
            'record' => Pages\Record::route('/{record}/record'),
        ];
    }
}