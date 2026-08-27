<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\Location;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    public function run(): void
    {
        // Uzimamo sve postojeće lokacije iz baze
        $locations = Location::all();

        // Ako nema lokacija, kreiramo 3 osnovne
        if ($locations->isEmpty()) {
            $locations = Location::factory(3)->create();
        }

        // Za svaku lokaciju kreiramo po 5 automobila
        foreach ($locations as $location) {
            Car::factory(5)->create([
                'location_id' => $location->id,
            ]);
        }
    }
}
