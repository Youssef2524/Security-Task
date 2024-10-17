<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use SoftDeletes;
    protected $fillable = ['title', 'description', 'type', 'status', 'priority', 'due_date', 'assigned_to'];

    protected $dates = ['deleted_at'];

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    // Define the relationship with the Comment model
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
    // Define the relationship with the Attachment model
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    // Define the relationship with the TaskStatusUpdate model
    public function statusUpdates()
    {
        return $this->hasMany(TaskStatusUpdate::class);
    }
    // Define the relationship with the Task model
    public function dependencies()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'task_id', 'dependent_task_id');
    }
    // Define the relationship with the Task model
    public function dependents()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'dependent_task_id', 'task_id');
    }

    //scope for filter tasks by priority, status, type, assigned_to, due_date 
    public function scopeFilter(Builder $query, $priority, $status, $type, $assigned_to, $due_date)
    {
        if (!empty($priority)) {
            $query->where('priority', '=', $priority);
        }
        if (!empty($status)) {
            $query->where('status', '=', $status);
        }
        if (!empty($type)) {
            $query->where('type', '=', $type);
        }
        if (!empty($assigned_to)) {
            $query->where('assigned_to', '=', $assigned_to);
        }
        if (!empty($due_date)) {
            $query->where('due_date', '=', $due_date);
        }
       
        return $query;
    }
}
