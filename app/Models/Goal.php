<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use App\Models\ClassSubject;

class Goal extends Model
{
    protected $fillable = [
        'student_id', 'class_subject_id', 'title', 'description',
        'goal_type', 'start_date', 'end_date', 'status', 'priority', 'is_private'
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
