<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Role;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    // protected static ?string $activeNavigationIcon = 'heroicon-s-users';

    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Accounts';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('profile')
                ->disk('public')
                ->directory('user-profile')
                ->image()
                ->imageEditor()
                ->imageEditorAspectRatios([
                    '16:9',
                    '4:3',
                    '1:1',
                ])
                // ->imageEditorMode(2)
                ->columnSpan(2)
                ->required()
                ,
                Section::make()
                ->description('This will be display as user account')
                ->schema([
                    TextInput::make('name')->required(),
                    TextInput::make('email')->required()->unique(ignoreRecord: true),
                    Select::make('role_id')
                    ->required()
                    ->label('Role')
                    ->options(Role::all()->pluck('name', 'id'))
                    ->searchable(),
                    TextInput::make('password')
                    ->label(fn (string $operation) => $operation =='create' ? 'Password' : 'New Password')
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    
                    ,
                ]),
               
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile')->url(fn (User $record): null|string => $record->profile ?  Storage::disk('public')->url($record->profile) : null)
                ->openUrlInNewTab(),
               TextColumn::make('name')->sortable()->searchable(),
               TextColumn::make('email')->searchable(),
               TextColumn::make('role.name')
               ->badge()
               ->color(fn (string $state): string => match ($state) {
                   
                   'Admin' => 'success',
                   'Vet' => 'info',
                   'Client' => 'warning',
               })
               ->searchable(),
              
            ])
            ->filters([
                SelectFilter::make('role_id')
                ->options(Role::all()->pluck('name', 'id'))
                ->searchable(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }    
}
