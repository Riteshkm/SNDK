<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $permissions = [
           'category-list',
           'category-create',
           'category-edit',
           'category-delete',
           'sub_categories-list',
           'sub_categories-create',
           'sub_categories-edit',
           'sub_categories-delete',
           'products-list',
           'products-create',
           'products-edit',
           'products-delete',
        ];
        foreach ($permissions as $permission) {
            $get = Permission::where('name', $permission)->count();
            if ($get == 0) {
                Permission::create(['name' => $permission]);
            }
        }
    }
}
