<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            Log::info('Login attempt received', [
                'email' => $request->email,
                'request_path' => $request->path(),
                'method' => $request->method()
            ]);

            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken('auth-token')->plainTextToken;
                
                Log::info('Login successful', ['user' => $user->email]);
                
                return response()->json([
                    'token' => $token,
                    'user' => $user->only(['name', 'email'])
                ]);
            }

            Log::warning('Invalid credentials', ['email' => $request->email]);
            
            return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], 401);

        } catch (ValidationException $e) {
            Log::error('Validation failed', [
                'errors' => $e->errors(),
                'email' => $request->email
            ]);
            
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Login error', [
                'message' => $e->getMessage(),
                'email' => $request->email
            ]);
            
            return response()->json([
                'message' => 'An error occurred during login'
            ], 500);
        }
    }
}
