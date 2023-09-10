<?php

namespace App\Filament\Clinic\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Animal;
use App\Models\Patient;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Appointment;
use App\Models\Examination;
use Illuminate\Support\Carbon;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
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
use Illuminate\Database\Eloquent\Builder;
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
                            Group::make()
                            ->relationship('appointment')
                            ->schema([
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

                        
                            ]),

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


        
                      
                       

                    ])->hidden(function ($record) {
                        if ($record->appointment) {
                            return false;
                        }
                        return true;
                    }),

                    
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
                                                 ->rows(5)
                                                ,
                
                
                
                                         
                                                TextArea::make('diagnosis')
                                                ->columnSpan(5)
                                                 ->rows(5)
                                                ,
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
                                  
                                    ->maxItems(1)
                                    ,
                                ]),
                

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('animal.name')->label('Pet name')->formatStateUsing(function (Patient $record) {
                    return ucfirst($record->animal?->name);
                }),
                TextColumn::make('patients.animal.name')
                    ->badge()
                    ->separator(',')
                    ->label('Prescriptions'),
                // TextColumn::make('patient_count')->label('Pet name') ->formatStateUsing(function (Patient $record) {
                //     return ucfirst($record->animal?->name);
                // }),

                // TextColumn::make('category.name')->label('Type ') ->formatStateUsing(function ($state) {
                //     return ucfirst($state);
                // }),

                //     TextColumn::make('patients_count')->counts('patients')
                //     ->label('Total Records')
                //     ->badge()
                //     ->color('success'),

                //     TextColumn::make('user')
                //     ->label('Owner')
                //     ->formatStateUsing(function (Animal $record) {
                //         return ucfirst($record?->user?->first_name . ' ' . $record?->user?->last_name);
                //     }),


                // TextColumn::make('animal.name')
                //     ->label('Pet Name')
                //     ->sortable(),
                // TextColumn::make('appointment.date')
                //     ->label('Reorded From')
                //     ->sortable()
                //     ->formatStateUsing(function (Patient $record) {

                //         if ($record->appointment) {
                //             $date = Carbon::parse($record?->appointment?->date);
                //             $formattedDate = $date->format('F d, Y');
                //             return $formattedDate . ' - Appointment';
                //         } else {
                //             return 'No Appointment';
                //         }


                //     })
                //     ,
                // Tables\Columns\TextColumn::make('exam_type')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('examination_date')
                //     ->date()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('temperature')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('crt')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('price')
                //     ->money()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([


                    Tables\Actions\EditAction::make('manage-prescription')->label('Manage Exam & Rx')
                        ->icon('heroicon-s-pencil')
                        ->color('success')
                        ->tooltip('dsad'),
                    Tables\Actions\Action::make('view record')->url(fn ($record): string => self::getUrl('record', ['record' => $record])),
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
            ->groups([
                GroupBy::make('animal.name')
                    ->label('Pet ')
                    ->collapsible(),
            ])
            ->groupsInDropdownOnDesktop();
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
                                InfoSection::make('Pet Owner ')


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
                                            ]),


                                        TextEntry::make('animal.user')->columnSpan(6)->label('Phone Number')
                                            ->formatStateUsing(fn (Patient $record): string => !empty($record->phone_number) ? $record->phone_number : 'N/S')

                                            ->columnSpan([
                                                'sm' => 2,
                                                'xl' => 3,
                                                '2xl' => 4,
                                            ]),
                                        TextEntry::make('animal.user')->columnSpan(6)->label('Address')
                                            ->formatStateUsing(fn (Patient $record): string => !empty($record->address) ? $record->address : 'N/S')

                                            ->columnSpan([
                                                'sm' => 2,
                                                'xl' => 3,
                                                '2xl' => 4,
                                            ]),
                                        TextEntry::make('animal.user.email')->columnSpan(6)->label('Email')
                                            ->columnSpan([
                                                'sm' => 2,
                                                'xl' => 3,
                                                '2xl' => 4,
                                            ]),

                                    ]),
                                InfoSection::make('Pets & Services')

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
                                                    ->columnSpan([
                                                        'sm' => 1,
                                                        'xl' => 2,
                                                        '2xl' => 2,
                                                    ]),
                                                TextEntry::make('animal.breed')
                                                    ->label('Breed')
                                                    ->columnSpan([
                                                        'sm' => 1,
                                                        'xl' => 2,
                                                        '2xl' => 2,
                                                    ]),
                                                TextEntry::make('animal.sex')
                                                    ->label('Sex')
                                                    ->columnSpan([
                                                        'sm' => 1,
                                                        'xl' => 2,
                                                        '2xl' => 2,
                                                    ]),
                                                TextEntry::make('animal.date_of_birth')
                                                    ->date()
                                                    ->hintIcon('heroicon-m-calendar-days')
                                                    ->label('Birth date')
                                                    ->columnSpan([
                                                        'sm' => 1,
                                                        'xl' => 2,
                                                        '2xl' => 2,
                                                    ]),

                                                TextEntry::make('animal.weight')
                                                    ->label('Weight')
                                                    ->columnSpan([
                                                        'sm' => 1,
                                                        'xl' => 2,
                                                        '2xl' => 2,
                                                    ]),

                                                ViewEntry::make('services')->columnSpan([
                                                    'sm' => 1,
                                                    'xl' => 2,
                                                    '2xl' => 8,
                                                ])
                                                    ->label('Requested Services *')

                                                    ->view('infolists.components.services-list'),




                                            ])

                                            ->label('Pet\'s Name')
                                            ->contained(false)
                                            ->columnSpan(6),
                                    ]),
                            ]),
                        Tabs\Tab::make('Pets')

                            ->icon('heroicon-m-sparkles')
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
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 2,
                                            ]),
                                        TextEntry::make('animal.breed')
                                            ->label('Breed')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 2,
                                            ]),
                                        TextEntry::make('animal.sex')
                                            ->label('Sex')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 2,
                                            ]),
                                        TextEntry::make('animal.date_of_birth')
                                            ->date()
                                            ->hintIcon('heroicon-m-calendar-days')
                                            ->label('Birth date')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 2,
                                            ]),

                                        TextEntry::make('animal.weight')
                                            ->label('Weight')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 2,
                                            ]),

                                        ViewEntry::make('services')->columnSpan([
                                            'sm' => 1,
                                            'xl' => 2,
                                            '2xl' => 8,
                                        ])
                                            ->label('Requested Services *')

                                            ->view('infolists.components.services-list'),




                                    ])

                                    ->label('Pet\'s Name')
                                    ->contained(false)

                                    ->columnSpan(6),

                                //                                 TextColumn::make('price')
                                // ->summarize(Sum::make()->label('Total'))
                            ]),
                        Tabs\Tab::make('Prescription Details')
                            ->icon('heroicon-m-newspaper')
                            ->schema([]),

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
