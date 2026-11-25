<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Carrier;
use GuzzleHttp\Promise\Create;

class CarrierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $carriers = [
            'Amana' => ['Casablanca'=> 35, 'Rabat'=> 35, 'Marrakech'=> 40, 'Tanger' => 40, 'Fes' => 40, 'Agadir' => 45, 'Oujda' => 50, 'Laayoune' => 60 ],
            'Cathedis' => ['Casablanca' => 30, 'Rabat' => 30, 'Marrakech' => 35, 'Tanger' => 35, 'Fes' => 35, 'Agadir' => 40, 'Oujda' => 45, 'Laayoune' => 55],
            'Ozone Express' => ['Casablanca' => 25, 'Rabat' => 30, 'Marrakech' => 35, 'Tanger' => 35, 'Fes' => 35, 'Agadir' => 40, 'Oujda' => 45, 'Laayoune' => 60],
        ];

        foreach ($carriers as $name => $rates){
            $carrier = Carrier::create(['name'=> $name]);
            foreach ($rates as $city => $price){
                $carrier->rates()->create(['city'=> $city, 'price'=> $price]);
            }
        }
    }
}
