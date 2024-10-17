<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permissions;
use Illuminate\Http\Request;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Services\PermissionService;
use App\Http\Controllers\Controller; 

class PermissionsController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function index()
    {
        $permissions = $this->permissionService->getAllPermissions();
        return response()->json($permissions);
    }

    public function store(StorePermissionRequest $request)
    {
        $permission = $this->permissionService->createPermission($request->validated());
        return response()->json(['message' => 'تمت إضافة الصلاحية بنجاح', 'permission' => $permission]);
    }

    public function update(UpdatePermissionRequest $request, $id)
    {
        $permission = $this->permissionService->updatePermission($id, $request->validated());
        return response()->json(['message' => 'تم تحديث الصلاحية بنجاح', 'permission' => $permission]);
    }

    public function show($id)
    {
        $permission = $this->permissionService->getPermissionById($id);
        return response()->json($permission);
    }

    public function delete($id)
    {
        $this->permissionService->deletePermission($id);
        return response()->json(['message' => 'تم حذف الصلاحية بنجاح']);
    }
}
