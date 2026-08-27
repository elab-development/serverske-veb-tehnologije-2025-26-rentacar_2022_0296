<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
use App\Http\Requests\UploadCarImageRequest;
use App\Http\Resources\CarResource;
use App\Services\CarService;
use Illuminate\Http\Request;

class CarController extends Controller
{
    protected CarService $carService;

    public function __construct(CarService $carService)
    {
        $this->carService = $carService;
    }

    public function index(Request $request)
    {
        $cars = $this->carService->getAllCars($request);
        return CarResource::collection($cars);
    }

    public function store(StoreCarRequest $request)
    {
        $car = $this->carService->createCar($request->validated());

        return response()->json(['success' => true, 'message' => 'Vozilo uspešno dodato.', 'data' => new CarResource($car->load('location'))], 201);
    }

    public function show($id)
    {
        $car = $this->carService->getCarById((int) $id);
        if (!$car) {
            return response()->json(['success' => false, 'message' => 'Vozilo nije pronađeno.'], 404);
        }
        return new CarResource($car);
    }

    public function update(UpdateCarRequest $request, $id)
    {
        $car = $this->carService->updateCar((int) $id, $request->validated());
        if (!$car) {
            return response()->json(['success' => false, 'message' => 'Vozilo nije pronađeno.'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Vozilo uspešno ažurirano.', 'data' => new CarResource($car)], 200);
    }

    public function destroy($id)
    {
        $deleted = $this->carService->deleteCar((int) $id);
        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'Vozilo nije pronađeno.'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Vozilo uspešno obrisano.'], 200);
    }

    public function uploadImage(UploadCarImageRequest $request, $id)
    {
        $car = $this->carService->uploadCarImage((int) $id, $request->file('image'));
        if (!$car) {
            return response()->json(['success' => false, 'message' => 'Vozilo nije pronađeno.'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Slika vozila uspešno otpremljena.', 'data' => new CarResource($car)], 200);
    }
}
