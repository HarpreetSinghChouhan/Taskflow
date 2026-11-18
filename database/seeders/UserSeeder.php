<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'System Administrator',
                'email' => 'admin@baselineitdevelopment.com',
                'phone' => '+91 98765 43210',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'designation' => 'Principal Systems Architect',
                'joining_date' => '2023-01-15',
                'status' => 'active',
            ],
            [
                'name' => 'Sarah Jenkins',
                'email' => 'pm@baselineitdevelopment.com',
                'phone' => '+91 98765 43211',
                'password' => Hash::make('password'),
                'role' => 'project_manager',
                'designation' => 'Senior Project Manager',
                'joining_date' => '2023-06-01',
                'status' => 'active',
            ],
            [
                'name' => 'Vikram Malhotra',
                'email' => 'tl@baselineitdevelopment.com',
                'phone' => '+91 98765 43212',
                'password' => Hash::make('password'),
                'role' => 'team_leader',
                'designation' => 'Lead Full-Stack Engineer',
                'joining_date' => '2024-02-10',
                'status' => 'active',
            ],
            [
                'name' => 'Rohan Verma',
                'email' => 'employee@baselineitdevelopment.com',
                'phone' => '+91 98765 43213',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'designation' => 'Software Engineer',
                'joining_date' => '2024-05-20',
                'status' => 'active',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
