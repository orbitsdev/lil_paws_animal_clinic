<?php

namespace App\Filament\Clinic\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Clinic;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clinic\Resources\ClinicResource\Pages;
use App\Filament\Clinic\Resources\ClinicResource\RelationManagers;
use App\Models\RequestAccess;

class ClinicResource extends Resource
{
    protected static ?string $model = RequestAccess::class;
    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?string $modelLabel = 'Received Requests';
    protected static ?int $navigationSort = 1;




    public static function getNavigationBadge(): ?string
{
    return static::getModel()::whereHas('patient', function($query) {
        $query->where('clinic_id', auth()->user()->clinic->id);
    })->count();

}



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
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
            
            TextColumn::make('fromClinic.name')
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
                        'completed' => 'success',
                        'rejected' => 'danger',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pending' => 'heroicon-o-ellipsis-horizontal-circle',
                        'accepted' => 'heroicon-o-check-circle',
                        'completed' => 'heroicon-s-check-circle',
                        'rejected' => 'heroicon-o-x-mark',
                    })
                        ->searchable(),
              
            ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->bulkActions([
                
            ])
            ->emptyStateActions([
              
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $clinicId = auth()->user()->clinic?->id;
                $query->whereHas('patient', function($query) use($clinicId){
                    $query->where('clinic_id', $clinicId);
                });
                // $query->whereHas('fromClinic', function($query) use($clinicId){
                //     $query->where('from_clinic_id', $clinicId);
                // })->whereHas('patient',function($query)use($clinicId){
                //     $query->where('clinic_id', '!=', $clinicId);
                // });
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
            'index' => Pages\ListClinics::route('/'),
            // 'create' => Pages\CreateClinic::route('/create'),
            'edit' => Pages\EditClinic::route('/{record}/edit'),
        ];
    }    
}
