<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Merchant;
use App\Models\Order;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CarrierSeeder::class);

        $demo = Merchant::factory()->create([
        'name'       => 'Demo Merchant',
        'store_name' => 'Demo Store',
        'email'      => 'demo@marocod.test',
        ]);

        Order::factory()->count(15)->for($demo)->create();
        
        Merchant::factory()->count(2)->has(Order::factory()->count(80))->create();

    }
}
