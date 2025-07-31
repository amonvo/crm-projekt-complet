<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vytvořte oprávnění
        $permissions = [
            'view-companies',
            'create-companies', 
            'edit-companies',
            'delete-companies',
            'view-contacts',
            'create-contacts',
            'edit-contacts', 
            'delete-contacts',
            'manage-users',
            'view-reports'
        ];
        
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        
        // Vytvořte role
        $adminRole = Role::create(['name' => 'admin']);
        $clientRole = Role::create(['name' => 'client']);
        
        // Přiřaďte oprávnění
        $adminRole->givePermissionTo(Permission::all());
        $clientRole->givePermissionTo(['view-companies', 'view-contacts']);
    }
}
