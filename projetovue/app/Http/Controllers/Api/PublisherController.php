<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PublisherController extends Controller
{
    public function index(): JsonResponse
    {
        $publishers = Publisher::all();
        return response()->json($publishers);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:publishers',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $publisher = Publisher::create($request->all());
        return response()->json($publisher, 201);
    }

    public function show(Publisher $publisher): JsonResponse
    {
        return response()->json($publisher);
    }

    public function update(Request $request, Publisher $publisher): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:publishers,email,' . $publisher->id,
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $publisher->update($request->all());
        return response()->json($publisher);
    }

    public function destroy(Publisher $publisher): JsonResponse
    {
        $publisher->delete();
        return response()->json(null, 204);
    }
}
