<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function createUser(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $role = Role::find($data['role_id']);
        $user->roles()->attach($role);

        return $user;
    }

    public function updateUser(array $data, User $user)
    {
        $user->update([
            'name' => $data['name']??$data->name,
            'email' => $data['email']??$data->email,
            'password' => isset($data['password']) ? Hash::make($data['password']) : $user->password,
            'role_user' => $data['role_id']??$data->role_id,
          
        ]);
        return $user;
    }

    public function deleteUser(User $user)
    {
        $user->delete();
    }
}
