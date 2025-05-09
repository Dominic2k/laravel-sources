<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassStudent extends Model
{
    public $timestamps = false;
    protected $primaryKey = ['class_id', 'student_id'];
    public $incrementing = false;
    
    protected $fillable = [
        'class_id', 'student_id'
    ];
    
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
    
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'user_id');
    }
}
