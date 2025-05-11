<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClassSubject extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'class_id', 'subject_id', 'teacher_id', 'schedule_info', 'room', 'status'
    ];
    
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
    
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'user_id');
    }
    
    public function goals()
    {
        return $this->hasMany(Goal::class);
    }
}

