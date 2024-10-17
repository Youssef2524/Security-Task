<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Permissions;
use Illuminate\Support\Facades\DB;

class PermissionService
{
    public function getAllPermissions()
    {
        // return Permissions::with('roles')->get();
        return Permissions::all();
    }

    public function createPermission(array $data)
    {
            $permission = Permissions::create([
                'name' => $data['name'],
                // 'description' => $data['description'] ?? null,
            ]);
            return $permission;
    }

    public function updatePermission(int $id, array $data)
    {
        $permission = Permissions::findOrFail($id);

            $permission->update([
                'name' => $data['name'] ?? $permission->name,
                // 'description' => $data['description'] ?? $permission->description,
            ]);
            return $permission;
    }

    public function deletePermission(int $id)
    {
        $permission = Permissions::findOrFail($id);

            $permission->delete();
    }

    public function getPermissionById(int $id)
    {
        return Permissions::findOrFail($id);
    }
}
