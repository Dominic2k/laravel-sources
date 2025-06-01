<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $fillable = [
        'password', 'email', 'full_name', 'birthday', 'last_login'
    ];
    
    protected $hidden = [
        'password'
    ];
    
    protected $casts = [
        'birthday' => 'date',
        'last_login' => 'datetime',
    ];
    
    
    public function student()
    {
        return $this->hasOne(Student::class);
    }
    
    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function goals()
{
    return $this->hasMany(Goal::class, 'student_id');
}

public function inClassPlans()
{
    return $this->hasMany(InClassPlan::class, 'student_id');
}

public function selfStudyPlans()
{
    return $this->hasMany(SelfStudyPlan::class, 'student_id');
}

}






