<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CarResource;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    public function index(Request $request)
    {
        $query = Car::with('location');

        // Pretraga po brendu ili modelu
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }

        // Filtriranje po dostupnosti
        if ($request->has('is_available')) {
            $query->where('is_available', filter_var($request->input('is_available'), FILTER_VALIDATE_BOOLEAN));
        }

        // Filtriranje po lokaciji
        if ($request->has('location_id')) {
            $query->where('location_id', $request->input('location_id'));
        }

        // Sortiranje (podrazumevano po id-u uzlazno)
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = $request->input('sort_order', 'asc');

        if (in_array($sortBy, ['brand', 'model', 'year', 'price_per_day', 'id'])) {
            $query->orderBy($sortBy, strtolower($sortOrder) === 'desc' ? 'desc' : 'asc');
        }

        // Paginacija (podrazumevano 10 po strani)
        $perPage = $request->input('per_page', 10);
        $cars = $query->paginate($perPage);

        return CarResource::collection($cars);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer',
            'price_per_day' => 'required|numeric',
            'location_id' => 'required|exists:locations,id',
            'license_plate' => 'nullable|string|unique:cars',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $car = Car::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Vozilo uspešno dodato.',
            'data' => new CarResource($car->load('location'))
        ], 201);
    }

    public function show($id)
    {
        $car = Car::with('location')->find($id);
        if (!$car) {
            return response()->json(['success' => false, 'message' => 'Vozilo nije pronađeno.'], 404);
        }
        return new CarResource($car);
    }

    public function update(Request $request, $id)
    {
        $car = Car::find($id);
        if (!$car) {
            return response()->json(['success' => false, 'message' => 'Vozilo nije pronađeno.'], 404);
        }

        $car->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Vozilo uspešno ažurirano.',
            'data' => new CarResource($car->load('location'))
        ], 200);
    }

    public function destroy($id)
    {
        $car = Car::find($id);
        if (!$car) {
            return response()->json(['success' => false, 'message' => 'Vozilo nije pronađeno.'], 404);
        }

        $car->delete();

        return response()->json(['success' => true, 'message' => 'Vozilo uspešno obrisano.'], 200);
    }

    public function uploadImage(Request $request, $id)
    {
        $car = Car::find($id);
        if (!$car) {
            return response()->json(['success' => false, 'message' => 'Vozilo nije pronađeno.'], 404);
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Obrisati staru sliku ako postoji
        if ($car->image && Storage::disk('public')->exists($car->image)) {
            Storage::disk('public')->delete($car->image);
        }

        $path = $request->file('image')->store('cars', 'public');
        $car->image = $path;
        $car->save();

        return response()->json([
            'success' => true,
            'message' => 'Slika vozila uspešno otpremljena.',
            'data' => new CarResource($car)
        ], 200);
    }
}
