<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = Permission::all();

        //attribution de tout les droits à l'admin
        $admin = Role::whereName('Admin')->first();

        foreach ($permissions as $permission){
            DB::table('role_permission')->insert([
                'role_id' => $admin->id,
                'permission_id' => $permission->id,
            ]);
        }

        //attribution de tout les droits à l'editeur à part création de role
        $editor = Role::whereName('Editor')->first();

        foreach ($permissions as $permission){
            if(!in_array($permission->name, ['edit_roles'])){
                DB::table('role_permission')->insert([
                    'role_id' => $editor->id,
                    'permission_id' => $permission->id,
                ]);
            }
        }

        //attribution du role écriture
        $viewer = Role::whereName('Viewer')->first();
        $viewerRoles = [
            'view_users',
            'view_roles',
            'view_orders',
            'view_products'
        ];

        foreach ($permissions as $permission){
            if(in_array($permission->name, $viewerRoles)){
                DB::table('role_permission')->insert([
                    'role_id' => $viewer->id,
                    'permission_id' => $permission->id,
                ]);
            }
        }
    }
}
