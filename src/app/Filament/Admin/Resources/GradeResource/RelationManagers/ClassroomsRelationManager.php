<?php

namespace App\Filament\Admin\Resources\GradeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ClassroomsRelationManager extends RelationManager
{
    protected static string $relationship = 'classrooms';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. X-A'),
                Forms\Components\Select::make('homeroom_teacher_id')
                    ->relationship('homeroomTeacher.user', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Forms\Components\TextInput::make('capacity')
                    ->numeric()
                    ->default(30)
                    ->minValue(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable(),
                Tables\Columns\TextColumn::make('homeroomTeacher.user.name')
                    ->label('Homeroom Teacher'),
                Tables\Columns\TextColumn::make('capacity'),
                Tables\Columns\TextColumn::make('classroom_students_count')
                    ->label('Students')
                    ->counts('classroomStudents')
                    ->badge(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
