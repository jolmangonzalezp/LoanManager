<?php

namespace Database\Seeders;

use App\UserBC\Infrastructure\Persistence\Model\PermissionModel;
use App\UserBC\Infrastructure\Persistence\Model\RoleModel;
use App\UserBC\Infrastructure\Persistence\Model\UserModel;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['slug' => 'users.view', 'name' => 'View Users', 'group' => 'users'],
            ['slug' => 'users.create', 'name' => 'Create Users', 'group' => 'users'],
            ['slug' => 'users.update', 'name' => 'Update Users', 'group' => 'users'],
            ['slug' => 'users.delete', 'name' => 'Delete Users', 'group' => 'users'],
            ['slug' => 'customers.view', 'name' => 'View Customers', 'group' => 'customers'],
            ['slug' => 'customers.create', 'name' => 'Create Customers', 'group' => 'customers'],
            ['slug' => 'customers.update', 'name' => 'Update Customers', 'group' => 'customers'],
            ['slug' => 'customers.delete', 'name' => 'Delete Customers', 'group' => 'customers'],
            ['slug' => 'loans.view', 'name' => 'View Loans', 'group' => 'loans'],
            ['slug' => 'loans.create', 'name' => 'Create Loans', 'group' => 'loans'],
            ['slug' => 'loans.update', 'name' => 'Update Loans', 'group' => 'loans'],
            ['slug' => 'loans.delete', 'name' => 'Delete Loans', 'group' => 'loans'],
            ['slug' => 'payments.view', 'name' => 'View Payments', 'group' => 'payments'],
            ['slug' => 'payments.create', 'name' => 'Create Payments', 'group' => 'payments'],
            ['slug' => 'payments.update', 'name' => 'Update Payments', 'group' => 'payments'],
            ['slug' => 'payments.delete', 'name' => 'Delete Payments', 'group' => 'payments'],
            ['slug' => 'reports.view', 'name' => 'View Reports', 'group' => 'reports'],
            ['slug' => 'roles.view', 'name' => 'View Roles', 'group' => 'roles'],
            ['slug' => 'roles.create', 'name' => 'Create Roles', 'group' => 'roles'],
            ['slug' => 'roles.update', 'name' => 'Update Roles', 'group' => 'roles'],
            ['slug' => 'roles.delete', 'name' => 'Delete Roles', 'group' => 'roles'],
        ];

        foreach ($permissions as $data) {
            PermissionModel::create([
                'id' => Uuid::uuid7()->toString(),
                'slug' => $data['slug'],
                'name' => $data['name'],
                'group' => $data['group'],
            ]);
        }

        $adminRole = RoleModel::create([
            'id' => Uuid::uuid7()->toString(),
            'slug' => 'admin',
            'name' => 'Administrador',
            'description' => 'Acceso total al sistema',
            'is_system' => true,
        ]);

        RoleModel::create([
            'id' => Uuid::uuid7()->toString(),
            'slug' => 'agent',
            'name' => 'Agente',
            'description' => 'Gestion de clientes, prestamos y pagos',
            'is_system' => true,
        ]);

        RoleModel::create([
            'id' => Uuid::uuid7()->toString(),
            'slug' => 'viewer',
            'name' => 'Consultor',
            'description' => 'Acceso de solo lectura',
            'is_system' => true,
        ]);

        $allIds = PermissionModel::whereIn('slug', array_column($permissions, 'slug'))->pluck('id', 'slug');

        $adminRole->permissions()->sync($allIds->values()->toArray());

        $agentSlugs = [
            'customers.view', 'customers.create', 'customers.update', 'customers.delete',
            'loans.view', 'loans.create', 'loans.update', 'loans.delete',
            'payments.view', 'payments.create', 'payments.update', 'payments.delete',
            'users.view',
        ];
        $agentRole = RoleModel::where('slug', 'agent')->first();
        $agentRole->permissions()->sync(
            PermissionModel::whereIn('slug', $agentSlugs)->pluck('id')->toArray()
        );

        $viewerSlugs = ['customers.view', 'loans.view', 'payments.view', 'reports.view', 'users.view'];
        $viewerRole = RoleModel::where('slug', 'viewer')->first();
        $viewerRole->permissions()->sync(
            PermissionModel::whereIn('slug', $viewerSlugs)->pluck('id')->toArray()
        );

        $adminUser = UserModel::where('username', 'admin')->first();
        if ($adminUser) {
            $adminUser->roles()->sync([$adminRole->id]);
        }
    }
}
