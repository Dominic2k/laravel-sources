<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_subject_id',
        'title',
        'description',
        'file_url',
        'semester',
        'achievement_date'
    ];

    protected $casts = [
        'achievement_date' => 'date',
        'semester' => 'integer'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'user_id');
    }

    public function classSubject()
    {
        return $this->belongsTo(ClassSubject::class);
    }
}
