<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AuthorController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $authors = Author::orderBy('created_at', 'desc')->get();
            Log::info('Fetching authors', ['count' => $authors->count()]);
            return response()->json($authors);
        } catch (\Exception $e) {
            Log::error('Error fetching authors:', [
                'message' => $e->getMessage()
            ]);
            return response()->json([
                'message' => 'Error fetching authors'
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            Log::info('Creating author with data:', $request->all());

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:authors',
                'phone' => 'required|string|max:15'
            ]);

            $author = Author::create($validated);
            Log::info('Author created successfully:', ['id' => $author->id]);
            
            return response()->json($author, 201);
        } catch (\Exception $e) {
            Log::error('Error creating author:', [
                'message' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return response()->json([
                'message' => 'Error creating author'
            ], 500);
        }
    }

    public function show(Author $author): JsonResponse
    {
        try {
            return response()->json($author);
        } catch (\Exception $e) {
            Log::error('Error fetching author:', [
                'id' => $author->id,
                'message' => $e->getMessage()
            ]);
            return response()->json([
                'message' => 'Error fetching author'
            ], 500);
        }
    }

    public function update(Request $request, Author $author): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:authors,email,' . $author->id,
                'phone' => 'required|string|max:15'
            ]);

            $author->update($validated);
            return response()->json($author);
        } catch (\Exception $e) {
            Log::error('Error updating author:', [
                'id' => $author->id,
                'message' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return response()->json([
                'message' => 'Error updating author'
            ], 500);
        }
    }

    public function destroy(Author $author): JsonResponse
    {
        try {
            $author->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error('Error deleting author:', [
                'id' => $author->id,
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Error deleting author'
            ], 500);
        }
    }
}
