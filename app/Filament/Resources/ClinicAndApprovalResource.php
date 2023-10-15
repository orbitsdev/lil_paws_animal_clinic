<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Clinic;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\ClinicAndApproval;
use Filament\Tables\Grouping\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ClinicAndApprovalResource\Pages;
use App\Filament\Resources\ClinicAndApprovalResource\RelationManagers;

class ClinicAndApprovalResource extends Resource
{
    protected static ?string $model = Clinic::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Request';
    protected static ?string $modelLabel = 'Clinics & Approval';


    public static function getPluralLabel(): ?string
    {
       return 'Clinic & Approval';

    } 
    protected static ?int $navigationSort = 6;

    public static function getNavigationBadge(): ?string
{
    return static::getModel()::where('from_request',true)->where('status', '!=', 'Accepted')->count();
}

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            
                Section::make()
                    ->description('This will be display as user account')
                    ->schema([

                        Select::make('user_id')
                        ->relationship(
                            name: 'owner',
                            modifyQueryUsing: fn (Builder $query) =>    $query->whereHas('role', function ($query){
                                $query->where('name','Veterenarian');
                            })->whereDoesntHave('ownedClinic')
                        )
                        ->label('Veterenarian Name')
                        ->hidden(fn (string $operation): bool => $operation === 'edit')
                        ->required()
                        ->getOptionLabelFromRecordUsing(fn (Model $record) => $record?->first_name . ' ' . $record->last_name)
                        ->preload()
                        ->native(false)
                        ->searchable()
                        ,
                        TextInput::make('name')->required(),
                        FileUpload::make('valid_id')
                        ->disk('public')
                        ->directory('user-valid-id')
                        ->image()
                        ->imageEditor()
                        ->imageEditorMode(2)
                        ->required()
                        ->columnSpanFull()
                        ->label('Valid ID')
                        ,
                        FileUpload::make('image')
                        ->disk('public')
                        ->directory('clinic')
                        ->image()
                        ->imageEditor()
                        ->columnSpanFull()
                        ->required()
                        ->label('Business Location')
                        ,

                                    
            Textarea::make('address')
            ->rows(5)
            ->required()
            ,

            Select::make('status')
            ->label('Request Status')
            ->options([
                'accepted' => 'Accepted',
                'pending' => 'Pending',
                'rejected' => 'Rejected',
            ])
            ->label('Status')
            ->required(),
            

                        // RichEditor::make('content')
                        //     ->toolbarButtons([

                        //         'blockquote',
                        //         'bold',
                        //         'bulletList',
                        //         'codeBlock',
                        //         'h2',
                        //         'h3',
                        //         'italic',
                        //         'link',
                        //         'orderedList',
                        //         'redo',
                        //         'strike',
                        //         'undo',
                        //     ])->required(),
                        // TextInput::make('address')->required(),





                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('user_id')
                ->formatStateUsing(function( $record){
                    if($record->owner){
                        return ucfirst($record?->owner?->first_name.' '.$record?->owner?->last_name);
                    }

                    return 'N/A';
                })
                ->label('Veterinarian')
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query->whereHas('owner', function ($query) use ($search) {
                        $query->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
                })
                ,
                ImageColumn::make('valid_id')->url(fn (Clinic $record): null|string => $record->valid_id ?  Storage::disk('public')->url($record->valid_id) : null)
                    ->openUrlInNewTab()
                    ->label('Valid ID')
                    ,
                ImageColumn::make('image')->url(fn (Clinic $record): null|string => $record->image ?  Storage::disk('public')->url($record->image) : null)
                    ->openUrlInNewTab()
                    ->label('Business Image')

                    ,
            
                TextColumn::make('address')->searchable()->markdown(),
                TextColumn::make('status')->searchable()->badge()->formatStateUsing(fn($state)=>  $state ? ucfirst($state) : '')
                ->color(fn (string $state): string => match ($state) {
                    'pending' => 'primary',
                    'accepted' => 'success',
                    'rejected' => 'danger',
                    default=> 'gray'
                })
                ,
            

            ])
            ->filters([
                SelectFilter::make('status')
                ->multiple()
                ->options([
                    'pending' => 'Pending',
                    'accepted' => 'Accepted',
                    'rejected' => 'Rejected',
                ])->label('Status'),
            ])
            ->actions([
                ActionGroup::make([

                    Tables\Actions\Action::make('Management')
                    ->icon('heroicon-s-pencil-square')
                    ->label('Manage Request')
                    ->color('success')
                    ->fillForm(function (Clinic $record, array $data) {
                        return [
                            'status' => $record->status
                        ];
                    })
                    ->form([

                        Select::make('status')
                            ->label('Request Status')
                            ->options([
                                'accepted' => 'Accepted',
                                'pending' => 'Pending',
                                'completed' => 'Completed',
                                'rejected' => 'Reject',
                            ])
                            
                            ->required()
                            
                            ,

                    ])
                    ->action(function (Clinic $record, array $data): void {
                        $record->status = $data['status'];
                        $record->save();
                    })
                    ,
                    Tables\Actions\EditAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                ]),
            ])
            ->emptyStateActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->groups([
                Group::make('status')
                ->titlePrefixedWithLabel(false)
                ->getTitleFromRecordUsing(fn (Clinic $record):  string => $record->status ? ucfirst($record->status) : ''),

            ])
            ->modifyQueryUsing(function (Builder $query) {
               $query->where('from_request', true);
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
            'index' => Pages\ListClinicAndApprovals::route('/'),
            'create' => Pages\CreateClinicAndApproval::route('/create'),
            'edit' => Pages\EditClinicAndApproval::route('/{record}/edit'),
        ];
    }    
}
