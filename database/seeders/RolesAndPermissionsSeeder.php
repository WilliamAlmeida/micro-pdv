<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $user->syncRoles([]);

        Permission::query()->delete();
        Role::query()->delete();
        DB::table('role_has_permissions')->delete();
        DB::table('model_has_roles')->delete();
        DB::table('model_has_permissions')->delete();

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $arrayOfPermissionsNames = [
            'countries', 'states', 'cities', 'users', 'roles', 'permissions', 'tenants', 'ncms', 'cfops', 'cests',
            'tenant.categories', 'tenant.products', 'tenant.suppliers', 'tenant.agreetments', 'tenant.clients', 'tenant.inventories', 'tenant.users', 'tenant.tenants'
        ];

        $crud = collect(['view', 'viewAny', 'create', 'edit', 'delete', 'forceDelete']);

        $permissions = collect($arrayOfPermissionsNames)->map(function ($permission) use ($crud) {
            $items = $crud->map(function ($item) use ($permission) { 
                return ['name' => "{$permission}.{$item}", 'guard_name' => 'web'];
            });
            return $items->all();
        })->collapse();

        Permission::insert($permissions->toArray());
        unset($crud);

        $arrayOfPermissionsNames = [
            'tenant.inventories.up', 'tenant.inventories.down',
        ];
        $permissions = collect($arrayOfPermissionsNames)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'web'];
        });

        Permission::insert($permissions->toArray());

        $arrayOfRolesNames = ['super admin', 'admin', 'client admin', 'client user'];
        $roles = collect($arrayOfRolesNames)->map(function ($role) {
            return ['name' => $role, 'guard_name' => 'web', 'tenant_id' => null];
        });

        Role::insert($roles->toArray());
        unset($arrayOfRolesNames, $roles);


        $role = Role::findByName('admin');
        $role->givePermissionTo(Permission::all());

        setPermissionsTeamId(1);
        $usuario = User::first();
        $usuario->syncRoles($role);

        /* client admin */
            $permissions = ['tenants.'];
            $role = Role::findByName('client admin');
            $results = Permission::where(function ($query) use ($permissions) {
                foreach ($permissions as $value) {
                    $query->orWhere('name', 'like', $value . '%');
                }
            })->get();
            $role->givePermissionTo($results);
        /* client admin */

        /* client user */
            $permissions = ['tenant.categories', 'tenant.products', 'tenant.suppliers', 'tenant.agreetments', 'tenant.clients', 'tenant.inventories'];
            $role = Role::findByName('client admin');
            $results = Permission::where(function ($query) use ($permissions) {
                foreach ($permissions as $value) {
                    $query->orWhere('name', 'like', $value . '%');
                }
            })->get();
            $role->givePermissionTo($results);
        /* client user */

















        // $permissions = ['view users', 'auth users', 'view tenants', 'create tenants', 'delete tenents', 'view posts', 'create posts', 'edit posts', 'delete posts'];

        // foreach ($permissions as $permission) {
        //     Permission::create(['name' => $permission]);
        // }

        // $permissions_all = Permission::get();

        // $permissions = ['view users', 'view tenants', 'create tenants', 'delete tenents', 'view posts', 'create posts', 'edit posts', 'delete posts'];
        // $permissions_admin = Permission::where(function ($query) use ($permissions) {
        //     foreach ($permissions as $value) {
        //         $query->orWhere('name', 'like', $value . '%');
        //     }
        // })->get();

        // $permissions = ['view tenants', 'view posts', 'create posts', 'edit posts'];
        // $permissions_writer = Permission::where(function ($query) use ($permissions) {
        //     foreach ($permissions as $value) {
        //         $query->orWhere('name', 'like', $value . '%');
        //     }
        // })->get();

        // $tenants = Tenant::get();

        // Role::create(['name' => 'super admin']);
        // Role::create(['name' => 'admin']);
        // Role::create(['name' => 'writer']);

        // $role = Role::findByName('super admin');
        // $role->givePermissionTo($permissions_all);

        // $role = Role::findByName('admin');
        // $role->givePermissionTo($permissions_admin);

        // $role = Role::findByName('writer');
        // $role->givePermissionTo($permissions_writer);

        // foreach ($tenants as $tenant) {
        //     Role::create(['name' => 'super admin', 'tenant_id' => $tenant->id]);
        //     Role::create(['name' => 'admin', 'tenant_id' => $tenant->id]);
        //     Role::create(['name' => 'writer', 'tenant_id' => $tenant->id]);

        //     $role = Role::whereTenantId($tenant->id)->whereName('super admin')->first();
        //     $role->givePermissionTo($permissions_all);

        //     $role = Role::whereTenantId($tenant->id)->whereName('admin')->first();
        //     $role->givePermissionTo($permissions_admin);

        //     $role = Role::whereTenantId($tenant->id)->whereName('writer')->first();
        //     $role->givePermissionTo($permissions_writer);
        // }

        // $user->assignRole('writer');

        // foreach ($user->tenants as $key => $tenant) {
        //     setPermissionsTeamId($tenant->id);
        //     $user->assignRole(!$key ? 'admin' : 'writer');
        // }
    }
}
