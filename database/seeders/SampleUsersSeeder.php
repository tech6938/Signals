<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SampleUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample users for testing
        $users = [
            [
                'f_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'password' => Hash::make('password'),
                'type' => 'subscriber',
                'status' => 'active',
            ],
            [
                'f_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@example.com',
                'password' => Hash::make('password'),
                'type' => 'simple_user',
                'status' => 'active',
            ],
            [
                'f_name' => 'Mike',
                'last_name' => 'Johnson',
                'email' => 'mike.johnson@example.com',
                'password' => Hash::make('password'),
                'type' => 'subscriber',
                'status' => 'active',
            ],
            [
                'f_name' => 'Sarah',
                'last_name' => 'Wilson',
                'email' => 'sarah.wilson@example.com',
                'password' => Hash::make('password'),
                'type' => 'simple_user',
                'status' => 'active',
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
