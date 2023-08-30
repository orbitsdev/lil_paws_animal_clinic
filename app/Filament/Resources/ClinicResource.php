<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Clinic;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ClinicResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ClinicResource\RelationManagers;

class ClinicResource extends Resource
{
    protected static ?string $model = Clinic::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Office';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('image')
                ->disk('public')
                ->directory('clinic')
                ->image()
                ->imageEditor()
                ->columnSpan(2)
                ->required(),

                Section::make()
                ->description('This will be display as user account')
                ->schema([
                

                    TextInput::make('name')->required(),
                    TextInput::make('address')->required(),
                  
                   
                   
                    
                    
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->url(fn (Clinic $record): null|string => $record->image ?  Storage::disk('public')->url($record->profile) : null)
                ->openUrlInNewTab(),
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('address')->searchable(),
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
            'index' => Pages\ListClinics::route('/'),
            'create' => Pages\CreateClinic::route('/create'),
            'edit' => Pages\EditClinic::route('/{record}/edit'),
        ];
    }    
}
