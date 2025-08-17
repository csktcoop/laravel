<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        function userBatchGenerator(int $total, int $batchSize): \Generator
        {
            $batch = [];

            for ($i = 0; $i < $total; $i++) {
                $batch[] = [
                    'name' => fake()->name(),
                    'email' => fake()->unique()->safeEmail(),
                    'email_verified_at' => now(),
                    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                    'remember_token' => Str::random(10),
                ];

                if (0 === ($i + 1) % $batchSize) {
                    yield $batch;
                    $batch = [];
                }
            }

            if ($batch) {
                yield $batch;
            }
        }

        // $total = 21;
        // $total = 2000; // For benchmark purpose
        $total = 0; // TODO @me: Remove this skip
        $batchSize = 500;

        foreach (userBatchGenerator($total, $batchSize) as $batch) {
            DB::table('users')->insert($batch);
        }

        $this->call([
            ProductSeeder::class,
        ]);
    }
}
