<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Clinic;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Models\ClinicAndApproval;
use Filament\Tables\Grouping\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\Tabs;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;

use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Section as InfoSection;
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
                'accepted' => 'Accept',
                'pending' => 'Pending',
                'rejected' => 'Reject',
                'completed' => 'Complete',
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

                    Tables\Actions\ViewAction::make()->color('primary')->label('View Details')->modalWidth('5xl')
                    ->modalHeading('Clinic Details'),

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
    

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfoSection::make('Veterinarian  Details')


                ->columns([
                    'sm' => 3,
                    'xl' => 6,
                    '2xl' => 8,
                ])
                ->schema([
                    
                    TextEntry::make('owner')->columnSpan(6)->label('Owner')
                    ->formatStateUsing(fn ($record)=> $record->owner?->first_name. ' '.$record->owner?->last_name)
                        ->label('Name')
                        ->color('gray')
                        ->columnSpan([
                            'sm' => 2,
                            'xl' => 3,
                            '2xl' => 4,
                        ]),


                    TextEntry::make('owner')->columnSpan(6)->label('Phone Number')
                        ->formatStateUsing(fn ($record): string => !empty($record->owner?->phone_number) ? $record->owner?->phone_number : 'N/S')
                        ->color('gray')
                        ->columnSpan([
                            'sm' => 2,
                            'xl' => 3,
                            '2xl' => 4,
                        ]),
                    TextEntry::make('owner')->columnSpan(6)->label('Address')
                        ->formatStateUsing(fn ($record): string => !empty($record->owner?->address) ? $record->owner?->address : 'N/S')
                        ->color('gray')
                        ->columnSpan([
                            'sm' => 2,
                            'xl' => 3,
                            '2xl' => 4,
                        ]),
                    TextEntry::make('owner.email')->columnSpan(6)->label('Email')
                        ->color('gray')
                        ->columnSpan([
                            'sm' => 2,
                            'xl' => 3,
                            '2xl' => 4,
                        ]),

                        ImageEntry::make('valid_id')
                        ->disk('public')
                        ->width(400)
                        ->height(400)
                        ->url(fn ($state): string =>  $state ? Storage::url($state) : null)
                        ->openUrlInNewTab()
                        ->label('Valid ID ')

                        ->columnSpan([
                            'sm' => 1,
                            'xl' => 2,
                            '2xl' => 8,
                        ]),

                ]),

                InfoSection::make('Clinic Details')


                ->columns([
                    'sm' => 3,
                    'xl' => 6,
                    '2xl' => 8,
                ])
                ->schema([  

                    TextEntry::make('status')
                    ->columnSpan([
                        'sm' => 1,
                        'xl' => 8,
                        '2xl' => 8,
                    ])
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'accepted' => 'success',
                        'pending' => 'info',
                        'completed' => 'success',
                        'rejected' => 'danger',
                    })
                    ->label('Request Status')
                    ->formatStateUsing(fn (string $state): string => ucfirst ($state))
                    ,

                    TextEntry::make('name')
                    ->label('Clinic Name')
                    ->color('gray')
                    ->columnSpan([
                        'sm' => 1,
                        'xl' => 2,
                        '2xl' => 2,
                    ]),
                TextEntry::make('address')
                    ->label('Breed')
                    ->color('gray')
                    ->columnSpan([
                        'sm' => 1,
                        'xl' => 2,
                        '2xl' => 2,
                    ])
                    ->label('Business Location'),

                    ImageEntry::make('image')
                    ->disk('public')
                    ->width(400)
                    ->height(400)
                    ->url(fn ($state): string =>  $state ? Storage::url($state) : null)
                    ->openUrlInNewTab()
                    ->label('Business  Image')


                    ->columnSpanFull()
              

                    

                ]),
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
            'index' => Pages\ListClinicAndApprovals::route('/'),
            'create' => Pages\CreateClinicAndApproval::route('/create'),
            'edit' => Pages\EditClinicAndApproval::route('/{record}/edit'),
        ];
    }    
}
