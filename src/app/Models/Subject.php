<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    public function teacherSubjects(): HasMany
    {
        return $this->hasMany(TeacherSubject::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    public function contents(): HasMany
    {
        return $this->hasMany(SubjectContent::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
