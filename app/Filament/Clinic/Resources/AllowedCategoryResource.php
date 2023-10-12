<?php

namespace App\Filament\Clinic\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\AllowedCategory;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clinic\Resources\AllowedCategoryResource\Pages;
use App\Filament\Clinic\Resources\AllowedCategoryResource\RelationManagers;

class AllowedCategoryResource extends Resource
{
    protected static ?string $model = AllowedCategory::class;

    
    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Management';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('category_id')
                ->relationship(name: 'category', titleAttribute: 'name')
                ->searchable()
                ->preload()
                ->required()
                ->native(false)
                ->columnSpanFull()

                ->createOptionForm([
                    Forms\Components\TextInput::make('name')
                    ->label('New Category')
                    ->maxLength(191)
                    ->required(),
                ])
                ,
              
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable()
                    ->label('Name   ')
                    ,
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
            })
            ;
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
