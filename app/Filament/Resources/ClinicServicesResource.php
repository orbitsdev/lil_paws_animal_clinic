<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Clinic;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ClinicServices;
use Filament\Resources\Resource;
use Filament\Tables\Grouping\Group;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ClinicServicesResource\Pages;
use App\Filament\Resources\ClinicServicesResource\RelationManagers;

class ClinicServicesResource extends Resource
{
    protected static ?string $model = ClinicServices::class;

    protected static ?string $navigationIcon = 'heroicon-o-scissors';
    protected static ?string $navigationGroup = 'Management';
  

    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('clinic_id')
                ->relationship(name: 'clinic', titleAttribute: 'name')

                ->label('Clinic Name')
                ->required()
                ->preload()
                ->native(false)
                ->searchable()
                ->columnSpanFull()

                
                ,

               TextInput::make('name')
               ->required()
                    ->maxLength(191)
                    ->columnSpanFull()
                    ->label('Service Name')
                    ,
               
               TextInput::make('cost')
               ->required()
                    ->numeric()
                    ->columnSpanFull()
                    ->prefix('₱'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('clinic.name')->badge()->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('cost')
                    ->money('PHP')
                    ->sortable(),
              
            ])
            ->filters([
                SelectFilter::make('clinic_id')
                ->multiple()
                ->options(Clinic::query()->pluck('name', 'id'))->label('By Clinic'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])->label('Actions'),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->groups([
                Group::make('clinic.name')
                ->titlePrefixedWithLabel(false)
                ->getTitleFromRecordUsing(fn (ClinicServices $record):  string => $record->clinic ? ucfirst($record->clinic->name) : ''),

                    
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
            'index' => Pages\ListClinicServices::route('/'),
            'create' => Pages\CreateClinicServices::route('/create'),
            'edit' => Pages\EditClinicServices::route('/{record}/edit'),
        ];
    }    
}
