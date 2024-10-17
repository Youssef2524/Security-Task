<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        Permission::create(['name' => 'create_tasks']);
        Permission::create(['name' => 'edit_tasks']);
        Permission::create(['name' => 'delete_tasks']);
        Permission::create(['name' => 'view_tasks']);
        Permission::create(['name' => 'softdelete_tasks']);
        Permission::create(['name' => 'restore_tasks']);
        Permission::create(['name' => 'assign_tasks']);
        Permission::create(['name' => 'resign_tasks']);

    }
}
