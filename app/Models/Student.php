<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
        'user_id', 'student_code', 'admission_date', 'current_semester'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'class_students', 'student_id', 'class_id');
    }
    
    public function goals()
    {
        return $this->hasMany(Goal::class, 'student_id', 'user_id');
    }
}


