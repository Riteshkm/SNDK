<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $user = User::create([
            'name'=>'admin',
            'email'=>'admin@yopmail.com',
            'password'=>Hash::make('Admin@123'),
        ]);
        
        $role = Role::create(['name' => 'Admin']);
        $permissions = Permission::pluck('id','id')->all();
        $role->syncPermissions($permissions);
        $user->assignRole([$role->id]);
        $role1 = Role::create(['name' => 'Customer']);
        $permissions1 = Permission::where('name','products-list')->get()->pluck('id','id')->all();
        $role1->syncPermissions($permissions1);
    }
}
