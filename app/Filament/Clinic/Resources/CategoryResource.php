<?php

namespace App\Filament\Clinic\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clinic\Resources\CategoryResource\Pages;
use App\Filament\Clinic\Resources\CategoryResource\RelationManagers;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Management';
    protected static ?string $modelLabel = 'Pet Category';
    protected static ?int $navigationSort = 4;
    protected static bool $shouldRegisterNavigation = false;



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
              
                TextInput::make('name')
                    ->maxLength(191),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                      
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                    ToggleColumn::make('is_admin')::make('archived')->label('Archived'),
                   TextColumn::make('created_at')
                    ->date(),
            ])
            ->filters([

                Filter::make('archived')
                ->query(fn (Builder $query): Builder => $query->where('archived', true))->label('Archived'),
                
            ])
            ->actions([
                Tables\Actions\EditAction::make()->button()->outlined(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->modifyQueryUsing(function(Builder $query){
                $clinicId = auth()->user()->clinic?->id;
                $query->where('clinic_id', $clinicId);
            }); 

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
