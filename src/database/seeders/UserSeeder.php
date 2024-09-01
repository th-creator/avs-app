<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'employee']);

        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'firstName' => 'admin',
                'lastName' => 'example',
                'password' => bcrypt('example')
            ]
        );
        $user = User::where('email',$user->email)->first();
        $user->roles()->syncWithoutDetaching($role);
        
    }
}
