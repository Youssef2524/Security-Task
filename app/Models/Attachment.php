<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;
    protected $fillable = ['file_path', 'user_id', 'name', 'path', 'mime_type', 'alt_text'];

    public function attachable()
    
    {
        return $this->morphTo();
    }
    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
