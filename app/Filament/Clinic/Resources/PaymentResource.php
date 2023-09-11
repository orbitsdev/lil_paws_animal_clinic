<?php

namespace App\Filament\Clinic\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Payment;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clinic\Resources\PaymentResource\Pages;
use App\Filament\Clinic\Resources\PaymentResource\RelationManagers;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Record Payment')
                ->description('This information will be included in the clinic report as a clear proof of payment, ensuring transparency.')
    ->schema([
        TextInput::make('title')
        ->maxLength(191)
        ->columnSpanFull()
        ->columnSpanFull(),
        
    Textarea::make('description')
        ->maxLength(65535)
        ->columnSpanFull(),
    TextInput::make('amount')
    ->prefix('₱')
        ->numeric()
        ->columnSpanFull(),
        FileUpload::make('receipt_image')
        ->disk('public')->image()->directory('receipt')
        ->columnSpanFull()
        ->label('Proof of payment'),
    ]),
              
               
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patient_id')
                ->formatStateUsing(function( $record){
                    if($record->patient){
                        return ucfirst($record?->patient?->animal?->user?->first_name.' '.$record?->patient?->animal?->user?->last_name);
                    }

                    return 'N/A';
                })->label('Patient Owner')
                ->color('gray')
                ,
                 

              
                Tables\Columns\TextColumn::make('clinic_id')
                ->formatStateUsing(function( $record){

                    if($record->patient){

                        return ucfirst($record?->patient?->animal?->name);
                    }

                    return 'N/A';
                })->label('Patient Name')
                ->color('gray')
                ,
                 
                  
              
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->searchable()
                
                    ,
                Tables\Columns\TextColumn::make('description')
                ->wrap()
                    ->sortable(),
                    ImageColumn::make('receipt_image')->url(fn (Payment $record): null|string => $record->profile ?  Storage::disk('public')->url($record->profile) : null)
                    ->openUrlInNewTab()
                    ->height(90)
                    ->width(90)
                    ,
                Tables\Columns\TextColumn::make('created_at')
                    ->date('Y-m-d H:i: A')
                    ->label('Created')
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }    
}
