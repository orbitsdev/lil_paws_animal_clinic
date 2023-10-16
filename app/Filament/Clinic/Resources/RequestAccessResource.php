<?php

namespace App\Filament\Clinic\Resources;

use App\Filament\Clinic\Resources\RequestAccessResource\Pages;
use App\Filament\Clinic\Resources\RequestAccessResource\RelationManagers;
use App\Models\RequestAccess;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RequestAccessResource extends Resource
{
    protected static ?string $model = RequestAccess::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';
    protected static ?string $modelLabel = 'Request Logs';
    protected static ?int $navigationSort = 2;

    // protected static ?string $navigationGroup = 'Request';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('patient_id')
                ->relationship(
                    name: 'patient',
                    titleAttribute: 'id',
                    modifyQueryUsing:function($query){
                        $clinicId = auth()->user()->clinic?->id;
                         $query->where('clinic_id', '!=', $clinicId);
                    
                    }
                    
                )
                ->label('Pets Name')
                ->getOptionLabelFromRecordUsing(function (Model $record) {

                    return $record->animal->name . ' - ('. $record->animal->user->first_name. ''.$record->animal->user->last_name  ;
                    

                })

                ->preload()
                ->required()
                ->searchable(),
                Forms\Components\TextInput::make('from_clinic_id')
                    ->numeric(),
                Forms\Components\TextInput::make('to_clinic_id')
                    ->numeric(),
                Forms\Components\TextInput::make('description')
                    ->maxLength(191),
                Forms\Components\TextInput::make('status')
                    ->maxLength(191),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patient_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('from_clinic_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('to_clinic_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Send New Request'),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $clinicId = auth()->user()->clinic?->id;
                $query->whereHas('fromClinic', function($query) use($clinicId){
                    $query->where('from_clinic_id', $clinicId);
                })->whereHas('patient',function($query)use($clinicId){
                    $query->where('clinic_id', '!=', $clinicId);
                });
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
            'index' => Pages\ListRequestAccesses::route('/'),
            'create' => Pages\CreateRequestAccess::route('/create'),
            'edit' => Pages\EditRequestAccess::route('/{record}/edit'),
        ];
    }    
}
