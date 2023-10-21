<?php

namespace App\Filament\Clinic\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\AllowedCategory;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clinic\Resources\AllowedCategoryResource\Pages;
use App\Filament\Clinic\Resources\AllowedCategoryResource\RelationManagers;

class AllowedCategoryResource extends Resource
{
    protected static ?string $model = AllowedCategory::class;


    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $modelLabel = 'Set Pet Categories';
    
    protected static ?string $navigationGroup = 'Clinic Data Management';


    protected static ?int $navigationSort = 3;




    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Select::make('category_id')
                // ->options(Category::pluck('name','id'))
                // ->searchable()                
                // ->required()
                // ->native(false)
                // ->columnSpanFull()
                // ->label('Category Name'),
                Select::make('category_id')
                    ->relationship(name: 'category', titleAttribute: 'name')
                    ->createOptionForm([
                        
              TextInput::make('name')->label('Category Name')
                ->maxLength(191)
                ->required(),
                        ])
                        ->preload()
                        ->native(false)
                        ->columnSpanFull()
                         ->label('Category Name'),
                    

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable()
                    ->label('Name   '),
                ToggleColumn::make('archived')->label('Archived'),


            ])
            ->filters([
                Filter::make('archived')
                    ->query(fn (Builder $query): Builder => $query->where('archived', true))->label('Archived'),

            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([

                // Tables\Actions\CreateAction::make(),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $clinicId = auth()->user()->clinic?->id;
                $query->where('clinic_id', $clinicId);
            });
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
            'index' => Pages\ListAllowedCategories::route('/'),
            'create' => Pages\CreateAllowedCategory::route('/create'),
            'edit' => Pages\EditAllowedCategory::route('/{record}/edit'),
        ];
    }
}
