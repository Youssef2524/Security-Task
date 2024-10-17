<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Requests\UserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Controllers\Controller; 

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = User::with('roles.permissions')->get();
        return response()->json($users);
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->createUser($request->validated());
        return response()->json(['message' => 'تم إضافة المستخدم بنجاح', 'user' => $user]);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $updatedUser = $this->userService->updateUser($request->validated(), $user);
        return response()->json(['message' => 'تم تحديث بيانات المستخدم بنجاح', 'user' => $updatedUser]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $this->userService->deleteUser($user);
        return response()->json(['message' => 'تم حذف المستخدم بنجاح']);
    }
}