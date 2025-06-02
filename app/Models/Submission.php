<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $table = 'submission';

    protected $fillable = [
        'student_id',
        'deadline_id',
        'status',
        'submitted_day',
    ];

    // Enum cho trạng thái nộp bài
    const STATUS_MISSED = 'missed';
    const STATUS_LATE = 'late';
    const STATUS_PROCESS = 'process';
    const STATUS_DONE = 'done';

    /**
     * Quan hệ với model User (đóng vai trò là sinh viên)
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Quan hệ với model Deadline
     */
    public function deadline()
    {
        return $this->belongsTo(Deadline::class);
    }
}