<?php

namespace App\Filament\Clinic\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Clinic;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Appointment;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Infolists\Components\Tabs;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Section as InfoSection;
use App\Filament\Clinic\Resources\AppointmentResource\Pages;
use App\Filament\Clinic\Resources\AppointmentResource\RelationManagers;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $modelLabel = 'Appointment Request';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Repeater::make('patients')
                ->relationship()
                ->schema([

                    TextInput::make('animal.name')->formatStateUsing(fn($state)=> $state)
                    ->label('Your Pet\'s Name')
                        ,

                    // Select::make('animal_id')
                    //     ->label('Your Pet\'s Name')
                    //     ->relationship(

                    //         name: 'animal',
                    //         modifyQueryUsing: fn (Builder $query) => $query->whereHas('user', function ($query) {
                    //             $query->where('user_id', auth()->user()->id);
                    //         })
                    //     )
                    //     ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->name} {$record->breed}")
                    //     ->searchable(['name', 'breed'])
                    //     ->preload()
                    //     ->required(fn (string $operation): bool => $operation === 'create'),

                    // Select::make('services')
                    //     ->label('Pick Services for Your Pet\'s Best ')
                    //     ->relationship(name: 'services', titleAttribute: 'name')
                    //     ->multiple()
                    //     ->preload()
                    //     ->native(false)
                    //     ->searchable()


                ])
                ->hint('Let\'s Keep Things One of a Kind, Avoid duplication')
                ->label('Introduce Your Beloved Pets')
                ->columns(2)
                ->columnSpan(6)
             
    //             Section::make()
    //             ->description('Select Your Clinic and Schedule
    // ')->icon('heroicon-m-building-storefront')
    //             ->schema([
    //                 Select::make('clinic_id')
    //                     ->options(Clinic::query()->pluck('name', 'id'))
    //                     ->native(false)
    //                     ->label('What Clinic?')
    //                     ->required()
    //                     ->searchable(),

    //                 DatePicker::make('date')->required()->label('When?'),
    //                 TimePicker::make('time')
    //                     ->timezone('Asia/Manila')
    //                     ->helperText(new HtmlString('(e.g., 02:30:00 PM)'))
    //                     ->required()
    //                     ->label('What Time?'),

    //                 RichEditor::make('extra_pet_info')
    //                     ->toolbarButtons([
    //                         'blockquote',
    //                         'bold',
    //                         'bulletList',
    //                         'codeBlock',
    //                         'h2',
    //                         'h3',
    //                         'italic',
    //                         'link',
    //                         'orderedList',
    //                         'redo',
    //                         'strike',
    //                         'undo',
    //                     ])
    //                     ->label('Extra Pet Info (Optional 😊)')
    //                     ->helperText(new HtmlString('Add any extra details or notes about your appointment – it\'s your chance to shine! Whether it\'s your pet\'s condition, concerns, or special wishes, we\'re all ears. Let\'s make your visit paw-sitively purr-fect'))
    //             ])->columnSpan(6),



    //         
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               
                TextColumn::make('clinic.name')
                    ->formatStateUsing(fn (string $state): string => $state ? ucfirst($state) : $state)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('date')->date()->color('warning'),
                TextColumn::make('time')->date('h:i:s A'),
                TextColumn::make('extra_pet_info')
                    ->markdown()

                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        // Only render the tooltip if the column content exceeds the length limit.
                        return $state;
                    })
                    ->wrap()
                    ->label('Appointment Details'),
                TextColumn::make('patients.animal.name')
                    ->badge()
                    ->separator(',')
                    ->label('Patients')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('name', 'like', "%{$search}%");
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

                    Tables\Actions\Action::make('update')
                        ->icon('heroicon-s-pencil-square')
                        ->label('Manage Request')
                        ->color('success')
                        ->fillForm(function(Appointment $record, array $data){
                            return [
                                'status'=> $record->status
                            ];
                        })
                        ->form([

                            Select::make('status')
                                ->label('Request Status')
                                ->options([
                                    'Accepted' => 'Accepted',
                                    'Pending' => 'Pending',
                                    'Completed' => 'Completed',
                                    'Rejected' => 'Rejected',
                                ])
                                ->required(),

                        ])
                        ->action(function (Appointment $record, array $data): void {
                            
                            
                          $veterenarian_id =   match($data['status']){
                                'Accepted'=> auth()->user()->id,
                                'Completed'=> auth()->user()->id,
                                'Pending'=> null,
                                'Rejected'=> auth()->user()->id,
                                default=> null,
                            };

                            $record->veterinarian_id = $veterenarian_id;  
                            $record->status = $data['status'];  
                            $record->save();


                            // dd($data);
                            // $this->record->author()->associate($data['authorId']);
                            // $this->record->save();
                        })->hidden(function($record){
                               return match($record->status){
                                     'Accepted'=> auth()->user()->id == $record?->veterinarian?->id ? false : true ,
                                     'Completed'=> auth()->user()->id == $record?->veterinarian?->id ? false : true ,
                                     'Rejected'=> auth()->user()->id == $record?->veterinarian?->id ? false : true ,
                                     default => false,
                                };
                        }),
                    Tables\Actions\EditAction::make('manage-prescription')->label('Manage Exam & Rx')
                    ->icon('heroicon-s-pencil')
                    ->color('success')
                    ->tooltip('dsad')
                    ->form([
                        TextInput::make('dasd'),
                    ])
                    ->action(function (array $data): void {
                        // $this->record->author()->associate($data['authorId']);
                        // $this->record->save();
                    })
                    ,
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
                                            ->label('Clinic'),


                                        TextEntry::make('date')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 2,
                                            ])
                                            ->date()
                                            ->label('Date Schedule'),



                                        TextEntry::make('time')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 2,
                                            ])

                                            ->date('H:i:s A')->timeZone('Asia/Manila')
                                            ->label('Time Schedule'),


                                        TextEntry::make('extra_pet_info')
                                            ->columnSpan([
                                                'sm' => 1,
                                                'xl' => 2,
                                                '2xl' => 8,
                                            ])

                                            ->markdown()
                                            ->label('Extra Details'),

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
                                            ->columnSpan([
                                                'sm' => 2,
                                                'xl' => 3,
                                                '2xl' => 4,
                                            ]),


                                        TextEntry::make('user')->columnSpan(6)->label('Phone Number')
                                            ->formatStateUsing(fn (Appointment $record): string => !empty($record->user->phone_number) ? $record->user->phone_number : 'N/S')

                                            ->columnSpan([
                                                'sm' => 2,
                                                'xl' => 3,
                                                '2xl' => 4,
                                            ]),
                                        TextEntry::make('user')->columnSpan(6)->label('Address')
                                            ->formatStateUsing(fn (Appointment $record): string => !empty($record->user->address) ? $record->user->address : 'N/S')

                                            ->columnSpan([
                                                'sm' => 2,
                                                'xl' => 3,
                                                '2xl' => 4,
                                            ]),
                                        TextEntry::make('user.email')->columnSpan(6)->label('Email')
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
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }    
}
