<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskReviewEvent extends Model
{
    protected $fillable = [
        'task_id',
        'user_id',
        'type',
        'body',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}