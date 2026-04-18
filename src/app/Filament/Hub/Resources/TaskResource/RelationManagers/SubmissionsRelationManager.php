<?php

namespace App\Filament\Hub\Resources\TaskResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SubmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'submissions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('score')
                    ->numeric()
                    ->minValue(0)
                    ->label('Score'),
                Forms\Components\Textarea::make('feedback')
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->options([
                        'submitted' => 'Submitted',
                        'graded' => 'Graded',
                        'returned' => 'Returned',
                        'late' => 'Late',
                    ])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('student.user.name')
            ->columns([
                Tables\Columns\TextColumn::make('student.user.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('answer')
                    ->limit(50)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'submitted' => 'info',
                        'graded' => 'success',
                        'returned' => 'warning',
                        'late' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('score')
                    ->numeric(2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'submitted' => 'Submitted',
                        'graded' => 'Graded',
                        'returned' => 'Returned',
                        'late' => 'Late',
                    ]),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Grade')
                    ->mutateFormDataUsing(function (array $data): array {
                        if (isset($data['score']) && $data['status'] !== 'graded') {
                            $data['status'] = 'graded';
                            $data['graded_at'] = now();
                        }
                        return $data;
                    }),
            ])
            ->bulkActions([]);
    }
}
