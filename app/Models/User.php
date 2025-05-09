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
        'username', 'password', 'email', 'full_name', 'birthday'
    ];
    
    protected $hidden = [
        'password'
    ];
    
    protected $casts = [
        'birthday' => 'date',
        'last_login' => 'datetime',
    ];
    
    public function roles()
    {
        return $this->hasMany(UserRole::class);
    }
    
    public function student()
    {
        return $this->hasOne(Student::class);
    }
    
    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }
}


