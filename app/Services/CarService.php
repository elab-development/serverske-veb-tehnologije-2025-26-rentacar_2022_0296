<?php

namespace App\Services;

use App\Models\Car;
use App\Repositories\CarRepository;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class CarService
{
    protected CarRepository $carRepository;
    protected FileService $fileService;

    public function __construct(CarRepository $carRepository, FileService $fileService)
    {
        $this->carRepository = $carRepository;
        $this->fileService = $fileService;
    }

    public function getAllCars(Request $request)
    {
        return $this->carRepository->getFilteredAndPaginated($request);
    }

    public function getCarById(int $id): ?Car
    {
        return $this->carRepository->findById($id);
    }

    public function createCar(array $data): Car
    {
        return $this->carRepository->create($data);
    }

    public function updateCar(int $id, array $data): ?Car
    {
        $car = $this->carRepository->findById($id);
        if (!$car) {
            return null;
        }

        return $this->carRepository->update($car, $data);
    }

    public function deleteCar(int $id): bool
    {
        $car = $this->carRepository->findById($id);
        if (!$car) {
            return false;
        }

        $this->fileService->deleteFile($car->image);

        return $this->carRepository->delete($car);
    }

    public function uploadCarImage(int $id, UploadedFile $file): ?Car
    {
        $car = $this->carRepository->findById($id);
        if (!$car) {
            return null;
        }

        $this->fileService->deleteFile($car->image);

        $path = $this->fileService->uploadFile($file, 'cars');

        return $this->carRepository->update($car, ['image' => $path]);
    }
}
