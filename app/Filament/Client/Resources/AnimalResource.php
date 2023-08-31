<?php

namespace App\Filament\Client\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Animal;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Client\Resources\AnimalResource\Pages;
use App\Filament\Client\Resources\AnimalResource\RelationManagers;


class AnimalResource extends Resource
{
    protected static ?string $model = Animal::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';


    


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make()
                    ->description('Animal Profile ')
                    ->icon('heroicon-m-sparkles')


                    ->schema([
                        TextInput::make('name')->required()->label('Pet Name'),
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
                ImageColumn::make('image')->url(fn (Animal $record): null|string => $record->image ?  Storage::disk('public')->url($record->image) : null)
                    ->openUrlInNewTab()
                    ->height(200)
                    ->width(200),
                TextColumn::make('name')->sortable()->searchable(),
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
            ->deferLoading()
            ->filters([

                SelectFilter::make('sex')
                    ->options([
                        'Male' => 'Male',
                        'Female' => 'Female',
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make()->button()->outlined(),
                Tables\Actions\DeleteAction::make()->button()->outlined(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])

            ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', auth()->user()->id));
          
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
