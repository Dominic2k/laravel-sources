<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Student;
use App\Models\Subject;

class Classes extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'class_name', 'semester', 'start_date', 'end_date', 'status'
    ];
    
    public function students()
    {
        return $this->belongsToMany(Student::class, 'class_students', 'class_id', 'student_id');
    }
    
    public function user()
{
    return $this->belongsTo(User::class);
}

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subjects', 'class_id', 'subject_id')
            ->withPivot(['teacher_id', 'schedule_info', 'room', 'status']);
    }
    
}