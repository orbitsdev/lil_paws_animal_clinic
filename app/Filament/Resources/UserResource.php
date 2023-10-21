<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Role;
use App\Models\User;
use Filament\Tables;
use App\Models\Clinic;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
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

    protected static ?string $navigationGroup = 'System Access';

    protected static ?string $navigationLabel = 'Accounts';
    protected static ?int $navigationSort = 0;

    public $role;

    protected function handleRecordCreation(array $data): Model
{
    dd($data);
    return static::getModel()::create($data);
}

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
                    TextInput::make('first_name')->required(),
                    TextInput::make('last_name')->required(),
                    TextInput::make('phone_number')
                    
                    ->minLength(11)
                    ->maxLength(11)
                    ->required(),
                    TextInput::make('address')->required(),
                    TextInput::make('email')->required()->unique(ignoreRecord: true),
                    Select::make('role_id')
                    ->required()
                    ->label('Role')
                    ->options(Role::where('name', '!=', 'Veterenarian')->pluck('name', 'id'))
                    ->searchable()
                    ->live()
                    ->hidden(fn (string $operation): bool => $operation === 'edit')
                    ,

                    // Select::make('clinic_id')
                    // ->required()
                    // ->label('Clinic')
                    // ->options(Clinic::whereDoesntHave('owner')->pluck('name', 'id'))
                    // ->searchable()
                  
                    // ->visible(function(Get $get){
                    //     $role = Role::find($get('role_id'));
                    //     if(!empty($role)){
                    //         return $role->name == 'Veterenarian';
                    //     }
                    // })
                    // ,

                    // FileUpload::make('valid_id')
                    // ->disk('public')
                    // ->directory('user-valid-id')
                    // ->image()
                    // ->imageEditor()
                    // ->imageEditorMode(2)
                    // ->required()
                    // ->label('Valid ID')
                    // ->visible(function(Get $get){
                    //     $role = Role::find($get('role_id'));
                    //     if(!empty($role)){
                    //         return $role->name == 'Veterenarian';
                    //     }
                    // })
                    
                    // ,
                    // TextInput::make('clinic_name')->label('Clinic Name')
                    // ->required()
                    // ->hidden(function(Get $get){
                    //     $role = Role::find($get('role_id'));
                    //     if(!empty($role)){
                    //         return $role->name != 'Veterenarian';
                    //     }
                    // })
                    
                    // ,
                              
                    // Textarea::make('clinic_address')
                    // ->rows(5)
                    // ->required()
                    // ->visible(function(Get $get){
                    //     $role = Role::find($get('role_id'));
                    //     if(!empty($role)){
                    //         return $role->name == 'Veterenarian';
                    //     }
                    // })
                    
                    // ,
        
                    // FileUpload::make('clinic_image')
                    // ->disk('public')
                    // ->directory('clinic')
                    // ->image()
                    // ->imageEditor()
                    // ->imageEditorMode(2)
                    // ->required()
                    // ->label('Business Location Image')
                    // ->visible(function(Get $get){
                    //     $role = Role::find($get('role_id'));
                    //     if(!empty($role)){
                    //         return $role->name == 'Veterenarian';
                    //     }
                    // })
                    
                    // ,
                    
                    
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
                ->openUrlInNewTab()
                ->height(90)
                ->width(90)
                ,
               TextColumn::make('first_name')->sortable()->searchable(),
               TextColumn::make('last_name')->sortable()->searchable(),
               TextColumn::make('email')->searchable(),
               TextColumn::make('role.name')
               ->badge()
               ->color(fn (string $state): string => match ($state) {
                   
                   'Admin' => 'success',
                   'Veterenarian' => 'info',
                   'Client' => 'warning',
               })
               ->formatStateUsing(function (User $record){

                if ($record->role->name === 'Veterenarian') {

                    if($record->clinic){
                        return $record->role->name . ' - ' . $record->clinic?->name;
                    }else{
                        return $record->role->name . ' - No Clinic Assigned';
                    }
                    // // Check if the veterinarian has a clinic assigned
                    // $hasNoClinic = !$record->veterinarian || !$record->veterinarian->clinic;
                
                    // if ($hasNoClinic) {
                    //     return $record->role->name . ' - No Clinic Assigned';
                    // } else {
                    //     // Return the veterinarian's clinic name
                    //     return $record->role->name . ' - ' . $record->veterinarian->clinic->name;
                    // }
                }
                
                // Handle other roles or scenarios here
                return $record->role->name;
                
               } )
               ->searchable(),
              
            ])
            ->filters([
                SelectFilter::make('role_id')
                ->options(Role::all()->pluck('name', 'id'))
                ->searchable()
                ->label('Role')
                ,
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
            ])
            ->groups([
                'role.name',
                
            ]);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }    
}
