<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $properties = Property::latest()->paginate(20);
        return response()->json($properties);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $data = $request->validate([
            'property_type' => 'required|string|max:50',
            'project_name' => 'nullable|string|max:150',
            'developer_name' => 'nullable|string|max:150',
            'unit_number' => 'nullable|string|max:50',
            'bedrooms' => 'nullable|integer',
            'bathrooms' => 'nullable|integer',
            'size_sqft' => 'nullable|numeric',
            'floor_number' => 'nullable|string|max:20',
            'view_type' => 'nullable|string|max:50',
            'parking_slots' => 'nullable|integer',
            'status_type' => 'nullable|in:Ready,Off-plan,Rented',
            'is_rented' => 'nullable|boolean',
            'rent_amount' => 'nullable|numeric',
            'contract_end_date' => 'nullable|date',
            'handover_date' => 'nullable|date',
            'outstanding_balance' => 'nullable|numeric',
            'asking_price' => 'nullable|numeric',
            'title_deed_url' => 'nullable|url',
            'spa_document_url' => 'nullable|url',
            'extra_files' => 'nullable|array',
            'source' => 'nullable|in:Website,WhatsApp,Manual Entry',
        ]);

        // attach seller_id from middleware (auth_user)
        $authUser = $request->attributes->get('auth_user');
        if ($authUser && isset($authUser['id'])) {
            $data['seller_id'] = $authUser['id'];
        }

        $property = Property::create($data);

        return response()->json([
            'message' => 'Property created',
            'property' => $property
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $property = Property::find($id);
        if (!$property) return response()->json(['error' => 'Not found'], 404);
        return response()->json($property);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $property = Property::find($id);
        if (!$property) return response()->json(['error' => 'Not found'], 404);

        $data = $request->validate([
            // same rules as store, all nullable
            'property_type' => 'sometimes|required|string|max:50',
            'project_name' => 'nullable|string|max:150',
            // ...
        ]);

        $property->update($data);
        return response()->json([
            'message' => 'Property updated',
            'property' => $property
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $property = Property::find($id);
            if (!$property) return response()->json(['error' => 'Not found'], 404);

            $property->delete();
            return response()->json(['message' => 'Property deleted']);
        }
}
