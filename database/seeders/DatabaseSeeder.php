<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Sale;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::select('id')
            ->orderByDesc('id')
            ->chunk(1000, function ($users) {
                foreach ($users as $user) {
                    $user->update(['status' => rand(0, 1)]);
                }
                // for ($i = 0; $i < 2000; $i++) {
                //     $sales = [];
                //     $now = now()->format('Y-m-d h:m:s');
                //     for ($i = 0; $i < 100; $i++) {
                //         $userId = rand(1, 3000000);
                //         $sales[] = [
                //             'user_id' => $userId,
                //             'region' => fake()->address(),
                //             'country' => fake()->country(),
                //             'item_type' => fake()->mimeType(),
                //             'sales_channel' => rand(0, 1),
                //             'order_priority' => fake()->randomLetter(),
                //             'order_date' => fake()->date(),
                //             'order_id' => $userId,
                //             'ship_date' => fake()->date(),
                //             'units_sold' => 1000,
                //             'unit_price' => 9,
                //             'unit_cost' => 9,
                //             'total_revenue' => 1000,
                //             'total_cost' => 1000,
                //             'total_profit' => 1000,
                //             'created_at' => $now,
                //             'updated_at' => $now,
                //         ];
                //     }
                //     Sale::insert($sales);
                // }
            });

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
