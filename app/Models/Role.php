<?php

namespace App\Models;

use App\Models\Permissions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    // Define the relationship with the Permissions model
    public function permissions()
    {
        return $this->belongsToMany(Permissions::class, 'permission_role', 'role_id', 'permission_id');
    }
    
    // Define the relationship with the User model
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
