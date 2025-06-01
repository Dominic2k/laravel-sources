<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InClassPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'date',
        'skills_module',
        'lesson_summary',
        'self_assessment',
        'difficulties_faced',
        'improvement_plan',
        'problem_solved',
        'additional_notes',
        'student_id'
    ];

    protected $casts = [
        'date' => 'date',
        'problem_solved' => 'boolean',
        'self_assessment' => 'integer',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'user_id');
    }

    public function subject()
    {
        return $this->belongsTo(InClassPlan::class);
    }

    public function deadlines()
    {
        return $this->morphMany(\App\Models\Deadline::class, 'target');
    }

}
