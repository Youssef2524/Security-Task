<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['body', 'user_id'];

    public function commentable()
    {
        return $this->morphTo();
    }

    /// Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
