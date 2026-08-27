<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CarResource;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarController extends Controller
{
    public function index()
    {
        return CarResource::collection(Car::with('location')->get());
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
}
