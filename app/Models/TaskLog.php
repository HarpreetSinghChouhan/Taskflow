<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskLog extends Model
{
    protected $fillable = [
        'task_id',
        'user_id',
        'log_date',
        'hours',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'log_date' => 'date',
            'hours'    => 'decimal:2',
        ];
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // The person who logged the work
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}