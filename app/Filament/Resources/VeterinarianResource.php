<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Clinic;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Veterinarian;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\VeterinarianResource\Pages;
use App\Filament\Resources\VeterinarianResource\RelationManagers;

class VeterinarianResource extends Resource
{
    protected static ?string $model = Veterinarian::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Office';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                ->description('Assigned System Account
                ')
                ->icon('heroicon-m-user')
                ->columns([
                    'sm' => 3,
                    'xl' => 6,
                    '2xl' => 8,
                ])
                ->schema([
                    Select::make('user_id')
                   ->label('Who\'s Account ')
                    ->options(User::whereHas('role', function($query){
                        $query->whereName('Vet');
                    })->pluck('name', 'id')->all())
                    ->searchable()
                    ->required()
                                ->columnSpan([
                'sm' => 2,
                'xl' => 3,
                '2xl' => 4,
            ]),


                    Select::make('clinic_id')
                    ->label('What Clinic')
                    ->options(Clinic::query()
                    ->pluck('name','id'))->required()
                    ->searchable()
                                ->columnSpan([
                'sm' => 2,
                'xl' => 3,
                '2xl' => 4,
            ]),
                ]),
               
                Section::make()
                ->description('Veterinarians Details.this will be displayed to the client when scheduling appointments.')
                ->icon('heroicon-m-identification')
                ->columns([
                    'sm' => 3,
                    'xl' => 6,
                    '2xl' => 6,
                ])
                ->schema([

                    FileUpload::make('profile')
                    ->disk('public')
                    ->directory('veterenarian-profile')
                    ->image()
                    
                    ->imageEditor()
                    ->required()->columnSpan([
                        'sm' => 2,
                        'xl' => 6,
                        '2xl' => 6,
                    ]),
                    
                    TextInput::make('first_name')->required()->columnSpan([
                        'sm' => 2,
                        'xl' => 3,
                        '2xl' => 2,
                    ]),
                    TextInput::make('last_name')->required()->columnSpan([
                        'sm' => 2,
                        'xl' => 3,
                        '2xl' => 2,
                    ]),
                    Select::make('gender')->options([
                        'Male' => 'Male',
                        'Female'=> 'Female',
                    ])->required()->columnSpan([
                        'sm' => 2,
                        'xl' => 3,
                        '2xl' => 2,
                    ]),
                   
                   
                                
                ]),

                Section::make()
                ->description('Contact Details')
                ->icon('heroicon-m-phone')
                ->columns([
                    'sm' => 3,
                    'xl' => 6,
                    '2xl' => 6,
                ])
                ->schema([
                    TextInput::make('phone_number')->columnSpan([
                        'sm' => 2,
                        'xl' => 3,
                        '2xl' => 6,
                    ]),
                    TextInput::make('address')->required()->columnSpan([
                        'sm' => 2,
                        'xl' => 3,
                        '2xl' => 6,
                    ]),
                ]),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->url(fn (Veterinarian $record): null|string => $record->profile ?  Storage::disk('public')->url($record->profile) : null)
                ->openUrlInNewTab(),
                TextColumn::make('first_name')->sortable()->searchable(),
                TextColumn::make('last_name')->sortable()->searchable(),
                TextColumn::make('gender')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Male' => 'info',
                    'Female' => 'danger',
                })
                ->searchable(),
                TextColumn::make('address')->searchable(),
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
            'index' => Pages\ListVeterinarians::route('/'),
            'create' => Pages\CreateVeterinarian::route('/create'),
            'edit' => Pages\EditVeterinarian::route('/{record}/edit'),
        ];
    }    
}
