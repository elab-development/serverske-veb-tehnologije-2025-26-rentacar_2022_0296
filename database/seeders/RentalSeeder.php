<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Database\Seeder;

class RentalSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $cars = Car::all();

        // Ako nema korisnika ili vozila, generišemo ih
        if ($users->isEmpty()) {
            $users = User::factory(3)->create(['role' => 'client']);
        }

        if ($cars->isEmpty()) {
            $cars = Car::factory(5)->create();
        }

        // Kreiramo 10 test rezervacija
        for ($i = 0; $i < 10; $i++) {
            Rental::factory()->create([
                'user_id' => $users->random()->id,
                'car_id' => $cars->random()->id,
            ]);
        }
    }
}
