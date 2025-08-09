<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'view-orders',
            'create-orders',
            'edit-orders',
            'delete-orders',
            'view-cutting',
            'create-cutting',
            'edit-cutting',
            'delete-cutting',
            'view-embroideries',
            'create-embroideries',
            'edit-embroideries',
            'delete-embroideries',
            'view-prints',
            'create-prints',
            'edit-prints',
            'delete-prints',
            'view-washes',
            'create-washes',
            'edit-washes',
            'delete-washes',
            'view-factories',
            'create-factories',
            'edit-factories',
            'delete-factories',
            'view-lines',
            'create-lines',
            'edit-lines',
            'delete-lines',
            'view-production-report',
            'create-production-report',
            'edit-production-report',
            'delete-production-report',
            'view-finishing-report',
            'create-finishing-report',
            'edit-finishing-report',
            'delete-finishing-report',
            'view-garments',
            'create-garments',
            'edit-garments',
            'delete-garments',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
