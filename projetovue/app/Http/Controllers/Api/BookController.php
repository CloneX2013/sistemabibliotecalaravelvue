<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $books = Book::with(['author', 'publisher'])->orderBy('created_at', 'desc')->get();
            Log::info('Fetching books', ['count' => $books->count()]);
            return response()->json($books);
        } catch (\Exception $e) {
            Log::error('Error fetching books:', [
                'message' => $e->getMessage()
            ]);
            return response()->json([
                'message' => 'Error fetching books'
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            Log::info('Creating book with data:', $request->all());

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'author_id' => 'required|exists:authors,id',
                'publisher_id' => 'required|exists:publishers,id',
                'isbn' => 'required|string|max:20|unique:books',
                'publication_year' => 'required|integer|min:1800|max:' . date('Y'),
                'pages' => 'required|integer|min:1',
                'language' => 'required|string|max:50'
            ]);

            $book = Book::create($validated);
            Log::info('Book created successfully:', ['id' => $book->id]);
            
            // Load relationships for the response
            $book->load(['author', 'publisher']);
            return response()->json($book, 201);
        } catch (\Exception $e) {
            Log::error('Error creating book:', [
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
                'message' => 'Error creating book'
            ], 500);
        }
    }

    public function show(Book $book): JsonResponse
    {
        try {
            $book->load(['author', 'publisher']);
            return response()->json($book);
        } catch (\Exception $e) {
            Log::error('Error fetching book:', [
                'id' => $book->id,
                'message' => $e->getMessage()
            ]);
            return response()->json([
                'message' => 'Error fetching book'
            ], 500);
        }
    }

    public function update(Request $request, Book $book): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'author_id' => 'required|exists:authors,id',
                'publisher_id' => 'required|exists:publishers,id',
                'isbn' => 'required|string|max:20|unique:books,isbn,' . $book->id,
                'publication_year' => 'required|integer|min:1800|max:' . date('Y'),
                'pages' => 'required|integer|min:1',
                'language' => 'required|string|max:50'
            ]);

            $book->update($validated);
            $book->load(['author', 'publisher']);
            return response()->json($book);
        } catch (\Exception $e) {
            Log::error('Error updating book:', [
                'id' => $book->id,
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
                'message' => 'Error updating book'
            ], 500);
        }
    }

    public function destroy(Book $book): JsonResponse
    {
        try {
            $book->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error('Error deleting book:', [
                'id' => $book->id,
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Error deleting book'
            ], 500);
        }
    }
}
