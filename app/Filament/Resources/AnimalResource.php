<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Animal;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Contracts\View\View;
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
use App\Filament\Resources\AnimalResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AnimalResource\RelationManagers;

class AnimalResource extends Resource
{
    protected static ?string $model = Animal::class;

   
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $modelLabel = 'Pets';

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
                // SelectFilter::make('category')
                // ->options(Category::pluck('name','id')),
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
