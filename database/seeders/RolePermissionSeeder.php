<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        $admin = Role::where('name', 'admin')->first();
        $manager = Role::where('name', 'manager')->first();
        $user = Role::where('name', 'user')->first();

        $createTasks = Permission::where('name', 'create_tasks')->first();
        $editTasks = Permission::where('name', 'edit_tasks')->first();
        $deleteTasks = Permission::where('name', 'delete_tasks')->first();
        $viewTasks = Permission::where('name', 'view_tasks')->first();
        $softdeleteTasks = Permission::where('name', 'softdelete_tasks')->first();
        $restoreTasks = Permission::where('name', 'restore_tasks')->first();
        $assignTasks = Permission::where('name', 'assign_tasks')->first();
        $resignTasks = Permission::where('name', 'resign_tasks')->first();


        // منح صلاحيات للأدوار
        $admin->permissions()->attach([$createTasks->id, $editTasks->id, $deleteTasks->id, $viewTasks->id,$softdeleteTasks->id,$resignTasks->id,$assignTasks->id,$restoreTasks->id]);
        $manager->permissions()->attach([$editTasks->id, $viewTasks->id]);
        $user->permissions()->attach([ $viewTasks->id]);
    }
}
