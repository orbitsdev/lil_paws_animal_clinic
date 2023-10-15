<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Clinic;
use App\Models\Payment;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PaymentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PaymentResource\RelationManagers;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';


    protected static ?string $navigationGroup = 'Management';
    protected static ?int $navigationSort = 9;
    protected static bool $shouldRegisterNavigation = false;



    // protected static ?int $navigationSort = 4;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Record Payment')
                ->description('This information will be included in the clinic report as a clear proof of payment, ensuring transparency.')
    ->schema([
        Select::make('clinic_id')
    ->relationship(name: 'clinic', titleAttribute: 'name')->label('Clinic To Record Payment')
    ->preload()
    ->native('false')
    ,
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
                Tables\Columns\TextColumn::make('clinic.name')
                ->searchable()
                ->badge()
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
            ])
            ->filters([
                SelectFilter::make('clinic_id')
                ->options(Clinic::query()->pluck('name', 'id'))->label('By Clinic'),
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
