<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\Permissions;
use App\Services\RoleService;
use App\Http\Requests\RoleRequest;
use App\Http\Requests\assirnRequest;
use App\Http\Controllers\Controller; 
use App\Http\Requests\updatePermissionRequest;
use App\Http\Requests\PermissionAssignmentRequest;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index()
    {
        $roles = Role::with('permissions')->get();
        return response()->json($roles);
    }

    public function show($id)
    {
        $role = Role::findOrFail($id);
        $role->load('permissions');
        return response()->json($role);
    }
    public function store(RoleRequest $request)
    {
        $role = $this->roleService->createRole($request->validated());
        return response()->json(['message' => 'تم إنشاء الدور بنجاح', 'role' => $role]);
    }

    public function assignPermissions(assirnRequest $request, $role_id)
    {
        $validatedData = $request->validated();
    
    $role = $this->roleService->assignPermissionsToRole($role_id, $validatedData['permission_id']);
    
    return response()->json(['message' => 'تم تحديث صلاحيات الدور بنجاح', 'role' => $role]);
    }
    public function destroy($id)
    {
        $this->roleService->deleteRole($id);
        return response()->json(['message' => 'تم حذف الدور بنجاح']);
    }
}
