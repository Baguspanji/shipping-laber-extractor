<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $products = [
            [
                'name' => 'URUNG KULIT BANTAL GULING',
                'price_take' => 10000,
                'price_sell' => 10000,
                'sku' => 'UB',
            ],
            [
                'name' => 'TIKAR SPON MERCY JUMBO KETEBALAN 2MM',
                'price_take' => 15000,
                'price_sell' => 20000,
                'sku' => 'TSM',
            ],
            [
                'name' => 'BANTAL GULING PREMIUM SET KAPAS ',
                'price_take' => 20000,
                'price_sell' => 25000,
                'sku' => 'GK',
            ],
            [
                'name' => 'KASUR LANTAI PALEMBANG 140X200X5CM',
                'price_take' => 20000,
                'price_sell' => 25000,
                'sku' => 'P140',
            ],
            [
                'name' => 'Keset Tenun Wajik Keset Kamar',
                'price_take' => 20000,
                'price_sell' => 25000,
                'sku' => 'KSW',
            ],
        ];

        foreach ($products as $product) {
            DB::table('products')->insert($product);
        }
    }
}
