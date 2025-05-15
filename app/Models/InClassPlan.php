<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InClassPlan extends Model
{
    //use HasFactory;
    protected $fillable = [
        'goal_id',
        'student_id',
        'class_subject_id',
        'date',
        'skills_module',
        'lesson_summary',
        'self_assessment',
        'difficulties_faced',
        'improvement_plan',
        'problem_solved',
        'additional_notes',
    ];
}
