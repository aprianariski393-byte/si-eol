<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $models = [
            'Asset',
            'AssetAttachment',
            'AssetHistory',
            'Category',
            'Department',
            'Location',
            'MaintenanceLog',
            'Permission',
            'Role',
            'SoftwareLicenseDetail',
            'Status',
            'User',
            'Vendor',
        ];

        $actions = [
            'View Any',
            'View',
            'Create',
            'Update',
            'Delete',
            'Restore',
            'Force Delete',
        ];

        $permissions = [];

        foreach ($models as $model) {
            $modelName = Str::title(Str::snake($model, ' '));
            foreach ($actions as $action) {
                $permissions[] = "{$action} {$modelName}";
            }
        }
        
        // Add widget permissions if any or let them be checked via roles
        
        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $administrator = Role::firstOrCreate(['name' => 'Administrator']);
        $administrator->syncPermissions($permissions); // All permissions

        $viewPermissions = array_filter($permissions, function($permission) {
            return str_starts_with($permission, 'View Any') || str_starts_with($permission, 'View');
        });

        $stafIT = Role::firstOrCreate(['name' => 'Staf IT']);
        $stafIT->syncPermissions($viewPermissions);

        $pimpinan = Role::firstOrCreate(['name' => 'Pimpinan']);
        $pimpinan->syncPermissions($viewPermissions);

        // Create users and assign roles
        $users = [
            [
                'name' => 'Administrator',
                'email' => 'admin@starter.com',
                'password' => Hash::make('12345678'),
                'role' => 'Administrator',
            ],
            [
                'name' => 'Staf IT',
                'email' => 'stafit@starter.com',
                'password' => Hash::make('12345678'),
                'role' => 'Staf IT',
            ],
            [
                'name' => 'Pimpinan',
                'email' => 'pimpinan@starter.com',
                'password' => Hash::make('12345678'),
                'role' => 'Pimpinan',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password'],
                ]
            );

            $user->syncRoles([$userData['role']]);
        }

        $this->command->info('Roles, Permissions, dan Users Telah berhasil dibuat!');
    }
}
