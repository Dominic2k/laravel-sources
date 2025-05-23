<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;

    public $timestamps = false;



    protected $fillable = [
        'class_name',
        'semester',
        'start_date',
        'end_date',
        'status'
    ];

    /**
     * Lấy danh sách sinh viên trong lớp
     */
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
            ->withPivot('teacher_id', 'status', 'room', 'schedule_info')
            ->withTimestamps();
    }
    
}
