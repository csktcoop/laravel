<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an admin user
        User::factory()
            ->state(['email' => 'admin@test.com', 'name' => 'Admin User', 'role' => 'admin'])
            ->create(['password' => bcrypt('password')]);

        // Create a regular user
        User::factory()
            ->state(['email' => 'user@test.com', 'name' => 'Regular User', 'role' => 'user'])
            ->create(['password' => bcrypt('password')]);

        // Create some additional users for testing
        // $testingUsers = 3;
        // $testingUsers = 1_000_000;
        // User::factory($testingUsers)->create();
    }
}
