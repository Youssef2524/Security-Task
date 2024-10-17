<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskDependency extends Model
{
    use HasFactory;
    protected $table = 'task_dependencies';
    protected $fillable = [
        'task_id',
        'dependent_task_id',
    ];
}
