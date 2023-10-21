<?php

namespace App\Filament\Clinic\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\RequestAccess;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clinic\Resources\RequestAccessResource\Pages;
use App\Filament\Clinic\Resources\RequestAccessResource\RelationManagers;



class RequestAccessResource extends Resource
{
    protected static ?string $model = RequestAccess::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';
    protected static ?string $modelLabel = 'Submitted Requests';

    protected static ?string $navigationGroup = 'Request Management';
    protected static ?int $navigationSort = 6;

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
                        $query->where('clinic_id', '!=', $clinicId)->latest()->whereDoesntHave('requestAccess', function($query) use ($clinicId) {
                            $query->where('from_clinic_id', $clinicId);
                        });
                    
                    }
                    
                )
                ->label('Pets Name')

                ->getOptionLabelFromRecordUsing(function (Model $record) {

                    return $record->animal->name . ' - '. $record->animal->user->first_name. ' '.$record->animal->user->last_name.' ('. $record->clinic->name.') '.Carbon::parse($record->created_at)->format('F d, Y l h:i A')  ;
                    
                    
                })
                
                ->preload()
                ->required()
                ->searchable()
                ->hint('Only Pet that has medical record will be shown here')
                ->helperText(new HtmlString('Patient - Owner ( Clinic ) | ( Record Date)')),
              
                // Forms\Components\TextInput::make('to_clinic_id'),
                   
                Textarea::make('description')
                ->columnSpanFull()
                
                // Forms\Components\TextInput::make('status')
                //     ->maxLength(191),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('patient.animal.user')
                ->formatStateUsing(fn ($state): string => $state ? ucfirst($state->first_name . ' ' . $state->last_name) : '')
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query->whereHas('patient.animal.user', function ($query) use ($search) {
                        $query->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
                }),
            TextColumn::make('patient.animal.name')
            ->formatStateUsing(fn ($state): string => ucfirst($state))
           
            ->label('Pet Owner')
            ->searchable(query: function (Builder $query, string $search): Builder {
                return $query->whereHas('patient.animal', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                });
            })
            ,
            
            TextColumn::make('patient.clinic.name')
                ->formatStateUsing(fn (string $state): string => $state ? ucfirst($state) : $state)
              
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query->whereHas('clinic', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
                })
                ->badge()
                ,

             
               TextColumn::make('description')
                    ->searchable(),
             TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state)=> $state ? ucfirst($state) : $state)
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'primary',
                        'accepted' => 'success',
                      
                        'rejected' => 'danger',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pending' => 'heroicon-o-ellipsis-horizontal-circle',
                        'accepted' => 'heroicon-o-check-circle',
                       
                        'rejected' => 'heroicon-o-x-mark',
                    })
                        ->searchable(),

                        ViewColumn::make('created_at')
                        ->view('tables.columns.download')
                        ->label('PDF File')
              
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                ActionGroup::make([
                    DeleteAction::make(),
                ]),
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
