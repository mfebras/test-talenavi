<?php

namespace Database\Seeders;

use App\Enums\TodoPriority;
use App\Enums\TodoStatus;
use App\Models\Todo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $table = (new Todo())->getTable();

        $assignees = [];
        for ($i=0; $i < 5; $i++) {
            $assignees[] = fake()->name();
        }

        $data = [];
        $priorities = TodoPriority::cases();
        $statuses = TodoStatus::cases();
        $startDate = now();
        $endDate = now()->addWeeks(2);
        foreach ($assignees as $assignee) {
            for ($k=0; $k < fake()->numberBetween(2, 5); $k++) {
                $randomDate = fake()->dateTimeBetween($startDate, $endDate);

                $data[] = [
                    'title'        => fake()->sentence(3),
                    'assignee'     => $assignee,
                    'due_date'     => $randomDate->format('Y-m-d'),
                    'time_tracked' => fake()->randomDigit(),
                    'status'       => $statuses[array_rand($statuses)]->value,
                    'priority'     => $priorities[array_rand($priorities)]->value,
                    'created_at'   => $startDate->format('Y-m-d H:i:s'),
                    'updated_at'   => $startDate->format('Y-m-d H:i:s'),
                ];
            }
        }

        try {
            DB::table($table)->insert($data);
        } catch (\Throwable $th) {
        }
    }
}
