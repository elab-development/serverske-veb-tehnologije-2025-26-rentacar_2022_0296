<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Location;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StatsController extends Controller
{
    // 1. Zbirni statisticki podaci o sistemu
    public function getDashboardStats()
    {
        $totalUsers = User::count();
        $totalCars = Car::count();
        $availableCars = Car::where('is_available', true)->count();
        $totalRentals = Rental::count();
        $totalRevenue = Rental::where('status', 'completed')->sum('total_price');

        return response()->json([
            'success' => true,
            'data' => [
                'total_users' => $totalUsers,
                'total_cars' => $totalCars,
                'available_cars' => $availableCars,
                'total_rentals' => $totalRentals,
                'total_revenue' => (float) $totalRevenue,
            ]
        ], 200);
    }

    // 2. Izvoz liste rezervacija u CSV formatu
    public function exportRentalsCsv()
    {
        $fileName = 'rentals_export_' . date('Y-m-d') . '.csv';
        $rentals = Rental::with(['user', 'car'])->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($rentals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Korisnik', 'Vozilo', 'Pocetni Datum', 'Krajnji Datum', 'Ukupna Cena', 'Status']);

            foreach ($rentals as $rental) {
                fputcsv($file, [
                    $rental->id,
                    $rental->user ? $rental->user->name : 'N/A',
                    $rental->car ? ($rental->car->brand . ' ' . $rental->car->model) : 'N/A',
                    $rental->start_date,
                    $rental->end_date,
                    $rental->total_price,
                    $rental->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
