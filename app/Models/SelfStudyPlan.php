<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SelfStudyPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'lesson',
        'time',
        'resources',
        'activities',
        'concentration',
        'plan_follow',
        'evaluation',
        'reinforcing'
    ];

    // Accessors để map các trường từ database sang tên mới
    public function getSkillsModuleAttribute()
    {
        return $this->lesson;
    }

    public function getLessonSummaryAttribute()
    {
        return $this->lesson;
    }

    public function getTimeAllocationAttribute()
    {
        return (int) $this->time;
    }

    public function getLearningResourcesAttribute()
    {
        return $this->resources;
    }

    public function getLearningActivitiesAttribute()
    {
        return $this->activities;
    }

    public function getConcentrationLevelAttribute()
    {
        return (int) $this->concentration;
    }

    public function getPlanFollowReflectionAttribute()
    {
        return $this->plan_follow;
    }

    public function getWorkEvaluationAttribute()
    {
        return $this->evaluation;
    }

    public function getReinforcingTechniquesAttribute()
    {
        return $this->reinforcing;
    }

    // Mutators để map các trường mới sang tên trong database
    public function setSkillsModuleAttribute($value)
    {
        $this->attributes['lesson'] = $value;
    }

    public function setLessonSummaryAttribute($value)
    {
        $this->attributes['lesson'] = $value;
    }

    public function setTimeAllocationAttribute($value)
    {
        $this->attributes['time'] = (string) $value;
    }

    public function setLearningResourcesAttribute($value)
    {
        $this->attributes['resources'] = $value;
    }

    public function setLearningActivitiesAttribute($value)
    {
        $this->attributes['activities'] = $value;
    }

    public function setConcentrationLevelAttribute($value)
    {
        $this->attributes['concentration'] = (string) $value;
    }

    public function setPlanFollowReflectionAttribute($value)
    {
        $this->attributes['plan_follow'] = $value;
    }

    public function setWorkEvaluationAttribute($value)
    {
        $this->attributes['evaluation'] = $value;
    }

    public function setReinforcingTechniquesAttribute($value)
    {
        $this->attributes['reinforcing'] = $value;
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'user_id');
    }

    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }
}