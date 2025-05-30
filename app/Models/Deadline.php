<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deadline extends Model
{
    protected $fillable = [
        'set_by',
        'title', 
        'description', 
        'due_date',
        'class_id',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];


    // Người tạo deadline
    public function setBy()
    {
        return $this->belongsTo(User::class, 'set_by');
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
}
