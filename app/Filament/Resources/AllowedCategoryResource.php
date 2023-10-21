<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Clinic;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\AllowedCategory;
use Filament\Resources\Resource;
use Filament\Tables\Grouping\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AllowedCategoryResource\Pages;
use App\Filament\Resources\AllowedCategoryResource\RelationManagers;

class AllowedCategoryResource extends Resource
{
    protected static ?string $model = AllowedCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $modelLabel = 'Set Clinic Pet Categories ';
    
    protected static ?string $navigationGroup = 'Clinic Data Management';

    protected static ?int $navigationSort = 4;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                
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
                SelectFilter::make('clinic_id')
                ->multiple()
                ->options(Clinic::query()->pluck('name', 'id'))->label('By Clinic'),
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
            ->groups([
                Group::make('clinic.name')
                ->titlePrefixedWithLabel(false)
                ->getTitleFromRecordUsing(fn (AllowedCategory $record): string => $record->clinic ?  ucfirst($record->clinic->name) : ''),

                    
            ])
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
