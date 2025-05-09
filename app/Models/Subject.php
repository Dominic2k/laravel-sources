<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Classes;
use App\Models\Teacher;

class Subject extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'subject_name', 'description'
    ];
    
    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'class_subjects', 'subject_id', 'class_id')
            ->withPivot(['teacher_id', 'schedule_info', 'room', 'status']);
    }
    
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'class_subjects', 'subject_id', 'teacher_id');
    }
}
