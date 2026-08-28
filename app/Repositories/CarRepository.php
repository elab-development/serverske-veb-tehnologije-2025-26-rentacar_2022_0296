<?php

namespace App\Repositories;

use App\Models\Car;
use App\Services\CurrencyService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CarRepository
{
    protected CurrencyService $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function getFilteredAndPaginated(Request $request): LengthAwarePaginator
    {
        $page = $request->input('page', 1);
        $search = $request->input('search', '');
        $isAvailable = $request->input('is_available', '');
        $locationId = $request->input('location_id', '');
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = $request->input('sort_order', 'asc');
        $perPage = (int) $request->input('per_page', 10);


        $cacheKey = "cars_page_{$page}_search_{$search}_avail_{$isAvailable}_loc_{$locationId}_sort_{$sortBy}_{$sortOrder}_pp_{$perPage}";

        return Cache::remember($cacheKey, 600, function () use ($request, $sortBy, $sortOrder, $perPage) {
            $query = Car::with('location');

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('brand', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%");
                });
            }

            if ($request->has('is_available') && $request->input('is_available') !== '') {
                $query->where('is_available', filter_var($request->input('is_available'), FILTER_VALIDATE_BOOLEAN));
            }

            if ($request->filled('location_id')) {
                $query->where('location_id', $request->input('location_id'));
            }

            if (in_array($sortBy, ['brand', 'model', 'year', 'price_per_day', 'id'])) {
                $query->orderBy($sortBy, strtolower($sortOrder) === 'desc' ? 'desc' : 'asc');
            }

            return $query->paginate($perPage);
        });
    }

    public function findById(int $id): ?Car
    {
        return Cache::remember("car_detail_{$id}", 3600, function () use ($id) {
            return Car::with('location')->find($id);
        });
    }

    public function create(array $data): Car
    {
        $car = Car::create($data);
        $this->clearCarsCache();
        return $car;
    }

    public function update(Car $car, array $data): Car
    {
        $car->update($data);
        $this->clearCarsCache($car->id);
        return $car->fresh('location');
    }

    public function delete(Car $car): bool
    {
        $carId = $car->id;
        $deleted = $car->delete();
        if ($deleted) {
            $this->clearCarsCache($carId);
        }
        return $deleted;
    }

    public function clearCarsCache(?int $carId = null): void
    {
        Cache::flush();
    }
}
