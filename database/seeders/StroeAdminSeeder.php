<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StroeAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'     => "Admin",
            'email'    => 'yousef1@admin.com',
            'password' => 'admin1234',
            'role_user' => "admin"
        ]);
        User::create([
            'name'     => "Manager",
            'email'    => 'yousef2@manager.com',
            'password' => 'manager1234',
            'role_user' => "manager"
        ]);
        User::create([
            'name'     => "User",
            'email'    => 'yousef3@user.com',
            'password' => 'user1234',
            'role_user' => "user"
            ]);

    }
}
