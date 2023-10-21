<?php

namespace App\Filament\Clinic\Resources;

use Filament\Forms;
use App\Models\Role;
use Filament\Tables;
use App\Models\Animal;
use App\Models\Clinic;
use Filament\Forms\Get;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clinic\Resources\AnimalResource\Pages;
use App\Filament\Clinic\Resources\AnimalResource\RelationManagers;

class AnimalResource extends Resource
{
    protected static ?string $model = Animal::class;

    
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $modelLabel = 'Pet & Owner';


    protected static ?string $navigationGroup = 'Clinic Data Management';


    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                ->description('Pet Profile ')
                ->icon('heroicon-m-sparkles')


                ->schema([
                    Select::make('user_id')
                    ->relationship(
                        name: 'user',
                        modifyQueryUsing: fn (Builder $query) =>    $query->whereHas('role', function ($query){
                            $query->where('name','Client');
                        })
                    )
                    ->label('Owner')
                    ->required()
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => $record?->first_name . ' ' . $record->last_name)
                    ->preload()
                    ->native(false)
                    ->searchable()
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
                            Select::make('role_id')
                            ->required()
                            ->label('Role')
                            ->options(Role::where('name', 'Client')->get()->pluck('name', 'id'))
                            ->searchable()
                            ->live()
                            ,
        
                            Select::make('clinic_id')
                            ->required()
                            ->label('Clinic')
                            ->options(Clinic::all()->pluck('name', 'id'))
                            ->searchable()
                          
                            ->hidden(function(Get $get){
                                $role = Role::find($get('role_id'));
                                if(!empty($role)){
                                    return $role->name != 'Veterenarian';
                                }
                            })
                            ,
                            
                            
                            TextInput::make('password')
                            ->label(fn (string $operation) => $operation =='create' ? 'Password' : 'New Password')
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            
                            ,
                        ]),
                    ])

                    
                    ,

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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id')
                ->formatStateUsing(function( $record){
                    if($record->user){
                        return ucfirst($record?->user?->first_name.' '.$record?->user?->last_name);
                    }

                    return 'N/A';
                })->label('Pet Owner')
                ->color('gray')
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query->whereHas('user', function ($query) use ($search) {
                        $query->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
                })
                ,
                ImageColumn::make('image')->url(fn (Animal $record): null|string => $record->image ?  Storage::disk('public')->url($record->image) : null)
                    ->openUrlInNewTab()
                    ->height(200)
                    ->width(200),
                TextColumn::make('name')->sortable()->searchable()->label('Pet Name')
                ->formatStateUsing(fn($state)=> ucfirst($state))
                ,
                TextColumn::make('category.name')
                ->sortable()
                ->searchable()
                ->label('Category')
                ->badge()
                ->color('primary')
                ->searchable()
                ,
                TextColumn::make('breed')->searchable(),
                TextColumn::make('sex')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Male' => 'info',
                        'Female' => 'danger',
                    })
                    ->searchable(),
                TextColumn::make('date_of_birth')->date()->searchable(),
                TextColumn::make('weight')->searchable(),
            ])
            ->filters([
                SelectFilter::make('sex')
                ->options([
                    'Male' => 'Male',
                    'Female' => 'Female',
                ]),
            ])
            ->actions([
                ActionGroup::make([
                    
                    Tables\Actions\EditAction::make(),
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
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnimals::route('/'),
            'create' => Pages\CreateAnimal::route('/create'),
            'edit' => Pages\EditAnimal::route('/{record}/edit'),
        ];
    }    
}
