<?php

namespace App\Filament\Clinic\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Clinic;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Appointment;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clinic\Resources\AppointmentResource\Pages;
use App\Filament\Clinic\Resources\AppointmentResource\RelationManagers;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';


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

                    DatePicker::make('date')->required()->label('When?'),
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


                
            Repeater::make('patients')
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
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->name} {$record->breed}")
                    ->searchable(['name', 'breed'])
                    ->preload()
                    ->required(fn (string $operation): bool => $operation === 'create')

                  
                    ,

                Select::make('services')
                ->label('Pick Services for Your Pet\'s Best ')    
                ->relationship(name: 'services', titleAttribute: 'name')
                    ->multiple()
                    ->preload()
                    ->native(false)
                    ->searchable()
                   

            ])
            ->hint('Let\'s Keep Things One of a Kind, Avoid duplication')
            ->label('Introduce Your Beloved Pets')
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
                ->label('Patients')
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
                SelectFilter::make('clinic_id')
                ->options(Clinic::query()->pluck('name','id'))->label('By Clinic')
                     ->default(fn()=> auth()->user()->veterinarian?->clinic_id)
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),

                
                Tables\Actions\Action::make('Update')
                ->button()
                ->outlined()
                ->label('Manage Status ')->icon('heroicon-o-pencil')
                ->form([
                    TextInput::make('subject')->required(),
                    RichEditor::make('body')->required(),
                ])
                ->action(function (Appointment $record, array $data) {


                    // Mail::to($this->client)
                    //     ->send(new GenericEmail(
                    //         subject: $data['subject'],
                    //         body: $data['body'],
                    //     ));
                })
                ,
                Tables\Actions\DeleteAction::make(),
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
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAppointments::route('/'),
        ];
    }    
}
