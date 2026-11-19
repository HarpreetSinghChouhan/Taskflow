<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'project_manager_id',
        'start_date',
        'deadline',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'deadline'   => 'date',
        ];
    }

    // The project manager who owns this project
    public function manager()
    {
        return $this->belongsTo(User::class, 'project_manager_id')->withTrashed();
    }
    // Tasks that belong to this project
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}