<?php

namespace App\Filament\Client\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Animal;
use App\Models\Clinic;
use App\Models\Service;
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
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Client\Resources\AppointmentResource\Pages;
use App\Filament\Client\Resources\AppointmentResource\RelationManagers;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';


    protected function handleRecordCreation(array $data): Model
    {
        dd($data);
        return static::getModel()::create($data);
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->description('Select Clinic & Services
            ')->icon('heroicon-m-user')
                    ->schema([
                        Select::make('clinic_id')
                            ->options(Clinic::query()->pluck('name', 'id'))
                            ->native(false)
                            ->label('What Clinic?'),
                        // Select::make('Select Services that you want in appointment')
                        //     ->multiple()
                        //     ->preload()
                        //     ->relationship(name: 'services', titleAttribute: 'name')
                        //     ->searchable()
                        //     ->native(false),
                        DatePicker::make('date'),
                        TimePicker::make('time'),

                        RichEditor::make('details')
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
                            ->label('Additional Details (Optional)')
                            ->helperText(new HtmlString('Add any additional details or notes about the appointment. This is optional and can help the clinic better understand your needs. You can include information about your pet\'s condition, concerns, or any special instructions.'))



                    ]),
                Section::make()
                    ->description('Set Patients
                            ')->icon('heroicon-m-user')
                    ->schema([
                        Repeater::make('patients')
                            ->relationship()
                            ->schema([
                                Select::make('patient.animal_id')->options(Animal::query()->pluck('name', 'id')),
                            ])

                    ]),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('clinic.name')->sortable()->searchable(),
                TextColumn::make('title')->sortable()->searchable(),
                TextColumn::make('date')->date(),
                TextColumn::make('time')->time(),
                TextColumn::make('details')->markdown(),
                TextColumn::make('status')->searchable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Approved' => 'Approved',
                        'Pending' => 'Pending',
                        'Completed' => 'Completed',
                    ])->label('By Gender'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAppointments::route('/'),
        ];
    }
}
