<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    public $timestamps = false;
    protected $primaryKey = ['user_id', 'role'];
    public $incrementing = false;
    
    protected $fillable = [
        'user_id', 'role'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}