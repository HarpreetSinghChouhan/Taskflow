<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskHourAllotment extends Model
{
    protected $fillable = [
        'task_id',
        'requested_by',
        'decided_by',
        'requested_hours',
        'approved_hours',
        'status',
        'note',
        'admin_note',
        'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'requested_hours' => 'decimal:2',
            'approved_hours'  => 'decimal:2',
            'decided_at'      => 'datetime',
        ];
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // The project manager who asked for the hours
    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by')->withTrashed();
    }

    // The admin who approved or rejected
    public function decider()
    {
        return $this->belongsTo(User::class, 'decided_by')->withTrashed();
    }
}