<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\TaskFactory;
use Domain\Task\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $user = User::where('email', 'user@test.com')->first();

        if (! $admin || ! $user) {
            return;
        }

        Task::factory()->create([
            'title' => 'Review team sprint goals',
            'description' => 'Validate the roadmap items and update the sprint board.',
            'status' => 'in_progress',
            'assigned_to' => $admin->id,
            'due_date' => now()->addDays(3)->toDateString(),
        ]);

        Task::factory()->create([
            'title' => 'Write onboarding documentation',
            'description' => 'Add setup and deployment notes for new contributors.',
            'status' => 'todo',
            'assigned_to' => $user->id,
            'due_date' => now()->addDays(7)->toDateString(),
        ]);

        Task::factory()->createMany([
            [
                'title' => 'Fix auth redirect bug',
                'description' => 'Confirm login flow returns users to the tasks page after auth.',
                'status' => 'todo',
                'assigned_to' => $user->id,
                'due_date' => now()->addDays(5)->toDateString(),
            ],
            [
                'title' => 'Publish API docs',
                'description' => 'Document task endpoints, filters, and expected responses.',
                'status' => 'done',
                'assigned_to' => $admin->id,
                'due_date' => now()->subDay()->toDateString(),
            ],
        ]);
    }
}
