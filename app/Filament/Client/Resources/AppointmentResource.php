<?php

namespace App\Filament\Client\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Clinic;
use App\Models\Patient;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Appointment;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;
use App\Filament\Client\Resources\AppointmentResource\Pages;
use App\Filament\Client\Resources\AppointmentResource\RelationManagers;
use Filament\Tables\Actions\ActionGroup;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    public static function getNavigationBadge(): ?string
{
    return static::getModel()::where('status','Pending')->count();
}

public static function getNavigationBadgeColor(): ?string
{
    return static::getModel()::where('status','Pending')->count() > 0 ? 'primary' : 'gray';
}

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                ->description('Select Your Clinic and Schedule
        ')->icon('heroicon-m-building-storefront')
                ->schema([
                    Select::make('clinic_id')
                        ->options(Clinic::query()->pluck('name', 'id'))
                        ->native(false)
                        ->label('What Clinic?')
                        ->required()
                        ->searchable()
                        
                        ,

                    DatePicker::make('date')->required()->label('When?')
                    ->timezone('Asia/Manila')
                    ->minDate( now()->toDateString())
                    ->closeOnDateSelection()
                    ,
                    TimePicker::make('time')
                        ->timezone('Asia/Manila')
                        ->helperText(new HtmlString('(e.g., 02:30:00 PM)'))
                        ->required()
                        ->label('What Time?')
                        
                        ,

                    RichEditor::make('extra_pet_info')
                        ->toolbarButtons([
                            'blockquote',
                            'bold',
                            'bulletList',
                            'codeBlock',
                            'h2',
                            'h3',
                            'italic',
                            'link',
                            'orderedList',
                            'redo',
                            'strike',
                            'undo',
                        ])
                        ->label('Extra Pet Info (Optional 😊)')
                        ->helperText(new HtmlString('Add any extra details or notes about your appointment – it\'s your chance to shine! Whether it\'s your pet\'s condition, concerns, or special wishes, we\'re all ears. Let\'s make your visit paw-sitively purr-fect'))
                     ])->columnSpan(6),


                
            TableRepeater::make('patients')
            ->relationship()
            ->schema([
                
                Select::make('animal_id')
                ->label('Your Pet\'s Name')    
                ->relationship(
                        name: 'animal',
                        modifyQueryUsing: fn (Builder $query) => $query->whereHas('user', function ($query) {
                            $query->where('user_id', auth()->user()->id);
                        })
                    )
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => ucfirst(optional($record)->name) .' - '. ucfirst(optional($record)->breed))
                    ->searchable(['animal.name', 'animal.breed'])
                    ->preload()
                  
                    ->label('Pet Name')
                  
                  
                    ,

                Select::make('services')
                ->label('Pick Services for Your Pet\'s Best ')    
                ->relationship(
                    name: 'services',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn (Builder $query, Get $get) => $query->when($get('animal_id'), function ($query) use ($get) {
                        $query->whereHas('categories.animals', function ($query) use ($get) {
                            $query->where('id', $get('animal_id'));
                        });
                    })
                     )
                     ->getOptionLabelFromRecordUsing(fn (Model $record) => optional($record)->name . ' - ₱' . number_format(optional($record)->cost))
                    ->multiple()
                    ->preload()
                    ->native(false)
                    ->searchable()
                   

            ])
            ->withoutHeader()
            ->hideLabels()
            ->hint('Let\'s Keep Things One of a Kind, Avoid duplication')
            ->label('Pets ')
            ->addActionLabel('Add Pet')
            ->columns(2)
            ->columnSpan(6)
            



            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('clinic.name')
                ->formatStateUsing(fn (string $state): string => $state ? ucfirst($state) : $state )
                ->sortable()
                ->searchable(),
                TextColumn::make('date')->date(),
                TextColumn::make('time')->date('h:i:s A'),
                TextColumn::make('extra_pet_info')
                ->markdown()
                
                ->tooltip(function (TextColumn $column): ?string {
                    $state = $column->getState();
             
                    if (strlen($state) <= $column->getCharacterLimit()) {
                        return null;
                    }
             
                    // Only render the tooltip if the column content exceeds the length limit.
                    return $state;
                })
                ->wrap()
                ->label('Appointment Details')
                ,
                TextColumn::make('patients.animal.name')
                ->badge()
                ->separator(',')
                ->formatStateUsing(fn($state) => ucFirst($state))
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query->whereHas('patient.animal', function($query) use ($search){
                        $query->where('name', 'like', "%{$search}%");
                    });
                        
                })
                ->label('Pets')
                ,
                TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Pending' => 'primary',
                    'Accepted' => 'success',
                    'Completed' => 'success',
                    'Rejected' => 'danger',
                })
                ->icon(fn (string $state): string => match ($state) {
                    'Pending' => 'heroicon-o-ellipsis-horizontal-circle',
                    'Accepted' => 'heroicon-o-check-circle',
                    'Completed' => 'heroicon-s-check-circle',
                    'Rejected' => 'heroicon-o-x-mark',
                })
                ->searchable(),
            ])
            ->filters([
                SelectFilter::make('status')
                ->options([
                    'Approved' => 'Approved',
                    'Pending' => 'Pending',
                    'Completed' => 'Completed',
                ])->label('By Gender'),
                SelectFilter::make('clinic_id')
                ->options(Clinic::query()->pluck('name', 'id'))->label('By Clinic'),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                    ->after(function ($record) {
                        $patients = Patient::where('appointment_id', $record->id)->get();
    
                        foreach ($patients as $patient) {
                            $patient->clinic_id = $record->clinic_id; // Set the clinic_id from the appointment
                            $patient->save();
                        }
                    }),
                    Tables\Actions\DeleteAction::make()
                ]),
             
             
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                ])->label('Delete Records'),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->poll('5s')
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
            'index' => Pages\ListAppointments::route('/'),
            // 'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }    
}
