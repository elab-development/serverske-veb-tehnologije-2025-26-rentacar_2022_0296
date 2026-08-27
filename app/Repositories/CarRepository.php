<?php

namespace App\Repositories;

use App\Models\Car;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class CarRepository
{
    public function getFilteredAndPaginated(Request $request): LengthAwarePaginator
    {
        $query = Car::with('location');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }

        if ($request->has('is_available')) {
            $query->where('is_available', filter_var($request->input('is_available'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->input('location_id'));
        }

        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = $request->input('sort_order', 'asc');

        if (in_array($sortBy, ['brand', 'model', 'year', 'price_per_day', 'id'])) {
            $query->orderBy($sortBy, strtolower($sortOrder) === 'desc' ? 'desc' : 'asc');
        }

        $perPage = (int) $request->input('per_page', 10);
        return $query->paginate($perPage);
    }

    public function findById(int $id): ?Car
    {
        return Car::with('location')->find($id);
    }

    public function create(array $data): Car
    {
        return Car::create($data);
    }

    public function update(Car $car, array $data): Car
    {
        $car->update($data);
        return $car->fresh('location');
    }

    public function delete(Car $car): bool
    {
        return $car->delete();
    }
}
