<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\RentalResource;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RentalController extends Controller
{
    public function index()
    {
        return RentalResource::collection(Rental::with(['user', 'car'])->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'car_id' => 'required|exists:cars,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $rental = Rental::create($request->all());

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

    public function update(Request $request, $id)
    {
        $rental = Rental::find($id);
        if (!$rental) {
            return response()->json(['success' => false, 'message' => 'Rezervacija nije pronađena.'], 404);
        }

        $rental->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Rezervacija uspešno ažurirana.',
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
