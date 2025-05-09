<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
        'user_id', 'specialization', 'join_date', 'bio'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subjects', 'teacher_id', 'subject_id');
    }
    
    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'class_subjects', 'teacher_id', 'class_id');
    }
}