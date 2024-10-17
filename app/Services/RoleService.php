<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Permissions;
use Illuminate\Support\Facades\DB;

class RoleService
{
    public function createRole(array $data)
    {
        
            $role = Role::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
            ]);

            return $role;
       
    }

    public function assignPermissionsToRole(int $roleId,$permissions)
    {
        $role = Role::findOrFail($roleId);

        $role->permissions()->attach($permissions);
        return $role;
    }

    public function deleteRole(int $roleId)
    {
        $role = Role::findOrFail($roleId);

            $role->delete();
    }
}
