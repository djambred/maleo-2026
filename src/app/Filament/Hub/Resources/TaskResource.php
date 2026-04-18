<?php

namespace App\Filament\Hub\Resources;

use App\Filament\Hub\Resources\TaskResource\Pages;
use App\Filament\Hub\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Academic';

    protected static ?string $navigationLabel = 'Tasks & Quizzes';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return true;
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        if ($user->hasRole('teacher') && $user->teacher) {
            return parent::getEloquentQuery()->where('teacher_id', $user->teacher->id);
        }

        if ($user->hasRole('student') && $user->student) {
            $classroomIds = $user->student->classroomStudents()->pluck('classroom_id');
            return parent::getEloquentQuery()
                ->where('is_published', true)
                ->whereIn('classroom_id', $classroomIds);
        }

        return parent::getEloquentQuery()->where('is_published', true);
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasRole('teacher');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Task Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('type')
                            ->options([
                                'assignment' => 'Assignment',
                                'quiz' => 'Quiz',
                                'exam' => 'Exam',
                            ])
                            ->default('assignment')
                            ->required(),
                        Forms\Components\TextInput::make('max_score')
                            ->numeric()
                            ->default(100)
                            ->minValue(0)
                            ->required(),
                        Forms\Components\Select::make('subject_id')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('classroom_id')
                            ->relationship('classroom', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('semester_id')
                            ->relationship('semester', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Hidden::make('teacher_id')
                            ->default(fn () => auth()->user()->teacher?->id),
                        Forms\Components\DateTimePicker::make('due_date'),
                        Forms\Components\Toggle::make('is_published')
                            ->default(false),
                    ]),
                Forms\Components\Section::make('Description')
                    ->schema([
                        Forms\Components\RichEditor::make('description')
                            ->columnSpanFull()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('task-attachments'),
                    ]),
                Forms\Components\Section::make('Attachment')
                    ->schema([
                        Forms\Components\FileUpload::make('attachment')
                            ->directory('tasks')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/*'])
                            ->maxSize(10240),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'assignment' => 'info',
                        'quiz' => 'warning',
                        'exam' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('subject.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('classroom.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_score')
                    ->numeric(0),
                Tables\Columns\TextColumn::make('due_date')
                    ->dateTime()
                    ->sortable()
                    ->color(fn ($record) => $record->due_date?->isPast() ? 'danger' : null),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean()
                    ->visible(fn () => auth()->user()->hasRole('teacher')),
                Tables\Columns\TextColumn::make('submissions_count')
                    ->label('Submissions')
                    ->counts('submissions')
                    ->badge()
                    ->color('success')
                    ->visible(fn () => auth()->user()->hasRole('teacher')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'assignment' => 'Assignment',
                        'quiz' => 'Quiz',
                        'exam' => 'Exam',
                    ]),
                Tables\Filters\SelectFilter::make('subject_id')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()->hasRole('teacher')),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()->hasRole('teacher')),
                Tables\Actions\Action::make('submit')
                    ->label('Submit')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn ($record) => auth()->user()->hasRole('student')
                        && !$record->submissions()->where('student_id', auth()->user()->student?->id)->exists())
                    ->form([
                        Forms\Components\Textarea::make('answer')
                            ->rows(5)
                            ->label('Your Answer'),
                        Forms\Components\FileUpload::make('attachment')
                            ->directory('submissions')
                            ->maxSize(10240)
                            ->label('Attachment'),
                    ])
                    ->action(function ($record, array $data) {
                        $record->submissions()->create([
                            'student_id' => auth()->user()->student->id,
                            'answer' => $data['answer'] ?? null,
                            'attachment' => $data['attachment'] ?? null,
                            'status' => 'submitted',
                            'submitted_at' => now(),
                        ]);
                    }),
            ])
            ->bulkActions([])
            ->defaultSort('due_date', 'asc');
    }

    public static function getRelations(): array
    {
        $user = auth()->user();

        if ($user && $user->hasRole('teacher')) {
            return [
                RelationManagers\SubmissionsRelationManager::class,
            ];
        }

        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
