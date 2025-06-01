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
        'user_id', 'specialization', 'bio', 'join_date'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subjects', 'teacher_id', 'subject_id');
    }
}