<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Clinic;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ClinicServices;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CategoryResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CategoryResource\RelationManagers;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Management';
    protected static ?string $modelLabel = 'Pet Category';


    protected static ?int $navigationSort = 5;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

              
                Forms\Components\TextInput::make('name')
                    ->maxLength(191)
                    ->required(),
                    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('clinic.name')->badge()->searchable(),
              
              
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                    TextColumn::make('allowed_categories.clinic.name')->formatStateUsing(function($state){
                        return $state;
                    })
    ->badge()
    ->wrap()
    ->listWithLineBreaks()
    ->separator(',')
    ->label('Clinic Availability')
    ,
                    
                    // ToggleColumn::make('archived')->label('Archived'),
                   TextColumn::make('created_at')
                    ->date(),
            ])
            ->filters([
                Filter::make('archived')
                ->query(fn (Builder $query): Builder => $query->where('archived', true))->label('Archived'),
               
                SelectFilter::make('clinic_id')
                ->multiple()
                ->options(Clinic::query()->pluck('name', 'id'))->label('By Clinic'),
                
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
            ->groups([
                Group::make('clinic.name')
                ->titlePrefixedWithLabel(false)
                ->getTitleFromRecordUsing(fn (Category $record): string => $record->clinic ?  ucfirst($record->clinic->name) : ''),

                    
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }    
}
