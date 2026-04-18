<?php

namespace App\Filament\Hub\Resources;

use App\Filament\Hub\Resources\SubjectContentResource\Pages;
use App\Models\SubjectContent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubjectContentResource extends Resource
{
    protected static ?string $model = SubjectContent::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Academic';

    protected static ?string $navigationLabel = 'Subject Content';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return true;
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        $query = parent::getEloquentQuery()->where('is_published', true);

        if ($user->hasRole('teacher') && $user->teacher) {
            $query = parent::getEloquentQuery()->where('teacher_id', $user->teacher->id);
        }

        if ($user->hasRole('student') && $user->student) {
            $classroomIds = $user->student->classroomStudents()->pluck('classroom_id');
            $query->whereIn('classroom_id', $classroomIds);
        }

        return $query;
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasRole('teacher');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Content Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
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
                        Forms\Components\TextInput::make('order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        Forms\Components\Toggle::make('is_published')
                            ->default(false),
                    ]),
                Forms\Components\Section::make('Content Body')
                    ->schema([
                        Forms\Components\RichEditor::make('body')
                            ->columnSpanFull()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('content-attachments'),
                    ]),
                Forms\Components\Section::make('Attachment')
                    ->schema([
                        Forms\Components\FileUpload::make('attachment')
                            ->directory('subject-content')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'image/*'])
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
                    ->limit(50),
                Tables\Columns\TextColumn::make('subject.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('classroom.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('teacher.user.name')
                    ->label('Teacher')
                    ->sortable(),
                Tables\Columns\TextColumn::make('order')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean()
                    ->visible(fn () => auth()->user()->hasRole('teacher')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subject_id')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('classroom_id')
                    ->relationship('classroom', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()->hasRole('teacher')),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()->hasRole('teacher')),
            ])
            ->bulkActions([])
            ->defaultSort('order');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubjectContents::route('/'),
            'create' => Pages\CreateSubjectContent::route('/create'),
            'edit' => Pages\EditSubjectContent::route('/{record}/edit'),
        ];
    }
}
