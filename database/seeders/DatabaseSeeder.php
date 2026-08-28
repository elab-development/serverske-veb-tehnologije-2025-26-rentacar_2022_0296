<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\Location;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Test korisnici sa razlicitim ulogama
        User::factory()->create([
            'name' => 'Admin Korisnik',
            'email' => 'admin@rentacar.com',
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Klijent Korisnik',
            'email' => 'client@rentacar.com',
            'role' => 'client',
        ]);

        $users = User::factory(5)->create(['role' => 'client']);

        // Kreiranje lokacija, vozila i rezervacija
        $locations = Location::factory(3)->create();

        foreach ($locations as $location) {
            $cars = Car::factory(4)->create(['location_id' => $location->id]);

            foreach ($cars as $car) {
                Rental::factory(2)->create([
                    'car_id' => $car->id,
                    'user_id' => $users->random()->id,
                ]);
            }
        }
    }
}
