<?php

namespace App\Filament\Clinic\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ClinicServices;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clinic\Resources\ClinicServicesResource\Pages;
use App\Filament\Clinic\Resources\ClinicServicesResource\RelationManagers;

class ClinicServicesResource extends Resource
{
    protected static ?string $model = ClinicServices::class;


    protected static ?string $navigationIcon = 'heroicon-o-scissors';
    protected static ?string $navigationGroup = 'Management';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               
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
                TextColumn::make('name')
                ->searchable(),
            TextColumn::make('cost')
                ->money('PHP')
                ->sortable(),
               TextColumn::make('created_at')
                    ->date()
                 
               
            ])
            ->filters([
                //
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
            'index' => Pages\ListClinicServices::route('/'),
            'create' => Pages\CreateClinicServices::route('/create'),
            'edit' => Pages\EditClinicServices::route('/{record}/edit'),
        ];
    }    
}
