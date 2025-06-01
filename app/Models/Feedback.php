<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = [
        'entity_type',
        'entity_id',
        'teacher_id',
        'field_name',
        'content',
    ];
}
