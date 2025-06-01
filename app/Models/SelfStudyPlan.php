<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SelfStudyPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'date',
        'lesson',
        'time',
        'resources',
        'activities',
        'concentration',
        'plan_follow',
        'evaluation',
        'reinforcing',
        'student_id'
    ];

    public function class()
     {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'user_id');
    }

    public function goal()
    {
        return $this->belongsTo(Goal::class, 'goal_id');
    }

    public function subject() 
    {
        return $this->belongsTo(Subject::class,'subject_id');
    }

    public function deadlines()
    {
        return $this->morphMany(\App\Models\Deadline::class, 'target');
    }

}