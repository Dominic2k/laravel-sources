<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = [
        'student_id',
        'class_subject_id',
        'title',
        'description',
        'file_url',
        'semester',
        'achievement_date'
    ];
}
