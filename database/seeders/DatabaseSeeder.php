<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'nickname' => 'admin',
            'phone' => '12345678',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'is_approved' => true,
            'is_admin' => true,
            'terms' => true,
            'rules' => true,
            'date_of_birth' => '1990-01-01',
        ]);

        // Create regular users
        $users = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'nickname' => 'johndoe',
                'phone' => '11111111',
                'email' => 'john@example.com',
                'date_of_birth' => '1992-03-15',
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'nickname' => 'janesmith',
                'phone' => '22222222',
                'email' => 'jane@example.com',
                'date_of_birth' => '1988-07-22',
            ],
            [
                'first_name' => 'Bob',
                'last_name' => 'Johnson',
                'nickname' => 'bobjohnson',
                'phone' => '33333333',
                'email' => 'bob@example.com',
                'date_of_birth' => '1995-11-30',
            ],
            [
                'first_name' => 'Alice',
                'last_name' => 'Williams',
                'nickname' => 'alicew',
                'phone' => '44444444',
                'email' => 'alice@example.com',
                'date_of_birth' => '1991-04-10',
            ],
            [
                'first_name' => 'Mike',
                'last_name' => 'Brown',
                'nickname' => 'mikebrown',
                'phone' => '55555555',
                'email' => 'mike@example.com',
                'date_of_birth' => '1993-09-05',
            ],
        ];

        foreach ($users as $userData) {
            User::create([
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'nickname' => $userData['nickname'],
                'phone' => $userData['phone'],
                'email' => $userData['email'],
                'password' => Hash::make('password123'), // Same easy password for all users
                'is_approved' => true,
                'is_admin' => false,
                'terms' => true,
                'rules' => true,
                'date_of_birth' => $userData['date_of_birth'],
            ]);
        }
    }
}
