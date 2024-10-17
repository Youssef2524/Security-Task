<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthService
{
     /*
      @param array $data
      @return  $user
  */
    public function register(array $data): User
    {
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->save();

        return $user;
    }
/*
      @param array $credentials
      @return  $token
  */
    public function login(array $credentials): ?string
    {
        if ($token = Auth::attempt($credentials)) {
            return $token;
        }

        return null;
    }
}
