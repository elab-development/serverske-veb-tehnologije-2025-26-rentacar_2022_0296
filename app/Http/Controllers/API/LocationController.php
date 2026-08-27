<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    public function index()
    {
        return LocationResource::collection(Location::all());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $location = Location::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Lokacija uspešno kreirana.',
            'data' => new LocationResource($location)
        ], 201);
    }

    public function show($id)
    {
        $location = Location::find($id);
        if (!$location) {
            return response()->json(['success' => false, 'message' => 'Lokacija nije pronađena.'], 404);
        }
        return new LocationResource($location);
    }

    public function update(Request $request, $id)
    {
        $location = Location::find($id);
        if (!$location) {
            return response()->json(['success' => false, 'message' => 'Lokacija nije pronađena.'], 404);
        }

        $location->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Lokacija uspešno ažurirana.',
            'data' => new LocationResource($location)
        ], 200);
    }

    public function destroy($id)
    {
        $location = Location::find($id);
        if (!$location) {
            return response()->json(['success' => false, 'message' => 'Lokacija nije pronađena.'], 404);
        }

        $location->delete();

        return response()->json(['success' => true, 'message' => 'Lokacija uspešno obrisana.'], 200);
    }
}
