<?php

namespace App\Filament\Clinic\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Examination;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clinic\Resources\ExaminationResource\Pages;
use App\Filament\Clinic\Resources\ExaminationResource\RelationManagers;

class ExaminationResource extends Resource
{
    protected static ?string $model = Examination::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-plus';
    protected static ?string $modelLabel = 'Medical Record';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('patient_id')
                    ->numeric(),
              
                Forms\Components\TextInput::make('exam_type')
                    ->maxLength(191),
                Forms\Components\DatePicker::make('examination_date'),
                Forms\Components\TextInput::make('temperature')
                    ->maxLength(191),
                Forms\Components\TextInput::make('crt')
                    ->maxLength(191),
                Forms\Components\Textarea::make('exam_result')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('image_result')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('diagnosis')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('$'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patient_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('exam_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('examination_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('temperature')
                    ->searchable(),
                Tables\Columns\TextColumn::make('crt')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
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
            'index' => Pages\ListExaminations::route('/'),
            'create' => Pages\CreateExamination::route('/create'),
            'edit' => Pages\EditExamination::route('/{record}/edit'),
        ];
    }    
}
