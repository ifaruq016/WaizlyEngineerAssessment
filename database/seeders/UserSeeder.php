<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'full_name' => 'John Doe',
                'email' => 'me@admin.com',
                'user_type' => 'ADMINISTRATOR',
                'status' => 'ACTIVE',
                'password' => Hash::make('admin')
            ]
        ];

        // Insert sample data into users table
        DB::table('users')->insert($users);
    }
}
