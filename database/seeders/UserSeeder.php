<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Dr. Muhammad Dwiya Lakhsmana',
                'email' => 'muhammaddwiyalakhsmana@gmail.com',
                'password' => Hash::make('password1'),
                'role' => 'admin',
            ],
            [
                'name' => 'Bob',
                'email' => 'bob@gmail.com',
                'password' => Hash::make('password2'),
                'role' => 'user',
            ],
            [
                'name' => 'Charlie',
                'email' => 'charlie@gmail.com',
                'password' => Hash::make('password3'),
                'role' => 'user',
            ],
            [
                'name' => 'David',
                'email' => 'david@gmail.com',
                'password' => Hash::make('password4'),
                'role' => 'guest',
            ],
            [
                'name' => 'Eve',
                'email' => 'eve@gmail.com',
                'password' => Hash::make('password5'),
                'role' => 'user',
            ],
            [
                'name' => 'Frank',
                'email' => 'frank@gmail.com',
                'password' => Hash::make('password6'),
                'role' => 'admin',
            ],
        ]);
    }
}
