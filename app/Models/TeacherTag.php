<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherTag extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'teacher_id',
        'tagged_by',
        'entity_type',
        'entity_id',
        'message',
        'resolved_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'user_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'tagged_by', 'user_id');
    }

    public function teacherUser()
    {
        return $this->belongsTo(User::class, 'teacher_id', 'id');
    }
}
