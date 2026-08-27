<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeRentalStatusRequest;
use App\Http\Requests\StoreRentalRequest;
use App\Http\Requests\UpdateRentalRequest;
use App\Http\Resources\RentalResource;
use App\Models\Rental;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    public function index(Request $request)
    {
        $query = Rental::with(['user', 'car']);

        // Filtriranje po statusu
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filtriranje po korisniku
        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // Sortiranje po datumu kreiranja ili ceni
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        if (in_array($sortBy, ['start_date', 'end_date', 'total_price', 'created_at'])) {
            $query->orderBy($sortBy, strtolower($sortOrder) === 'asc' ? 'asc' : 'desc');
        }

        // Paginacija
        $perPage = (int) $request->input('per_page', 10);
        $rentals = $query->paginate($perPage);

        return RentalResource::collection($rentals);
    }

    public function store(StoreRentalRequest $request)
    {
        $rental = Rental::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Rezervacija uspešno kreirana.',
            'data' => new RentalResource($rental->load(['user', 'car']))
        ], 201);
    }

    public function show($id)
    {
        $rental = Rental::with(['user', 'car'])->find($id);
        if (!$rental) {
            return response()->json(['success' => false, 'message' => 'Rezervacija nije pronađena.'], 404);
        }
        return new RentalResource($rental);
    }

    public function update(UpdateRentalRequest $request, $id)
    {
        $rental = Rental::find($id);
        if (!$rental) {
            return response()->json(['success' => false, 'message' => 'Rezervacija nije pronađena.'], 404);
        }

        $rental->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Rezervacija uspešno ažurirana.',
            'data' => new RentalResource($rental->load(['user', 'car']))
        ], 200);
    }

    public function changeStatus(ChangeRentalStatusRequest $request, $id)
    {
        $rental = Rental::find($id);
        if (!$rental) {
            return response()->json(['success' => false, 'message' => 'Rezervacija nije pronađena.'], 404);
        }

        $rental->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Status rezervacije uspešno izmenjen.',
            'data' => new RentalResource($rental->load(['user', 'car']))
        ], 200);
    }

    public function destroy($id)
    {
        $rental = Rental::find($id);
        if (!$rental) {
            return response()->json(['success' => false, 'message' => 'Rezervacija nije pronađena.'], 404);
        }

        $rental->delete();

        return response()->json(['success' => true, 'message' => 'Rezervacija uspešno obrisana.'], 200);
    }
}
