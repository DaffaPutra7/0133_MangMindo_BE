<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User; // Menggunakan model User
use Dedoc\Scramble\Support\Generator\Attributes\Group; // TAMBAHKAN INI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin;

class AuthController extends Controller
{
    /**
     * Login untuk user (bisa admin atau customer).
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');

        // Coba login sebagai Admin terlebih dahulu
        if ($token = Auth::guard('api_admin')->attempt($credentials)) {
            $user = Auth::guard('api_admin')->user();
            return $this->respondWithToken($token, 'admin', $user);
        }

        // Jika gagal, coba login sebagai Customer
        if ($token = Auth::guard('api')->attempt($credentials)) {
            $user = Auth::guard('api')->user();
            return $this->respondWithToken($token, 'customer', $user);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Registrasi untuk customer baru.
     */
    public function register(Request $request)
    {
        // ... (logika register tetap sama)
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        $token = Auth::guard('api')->login($user);
        return $this->respondWithToken($token, 'customer', $user);
    }

    /**
     * Helper untuk format response token.
     */
    protected function respondWithToken($token, $role, $user)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'role' => $role, // <-- Sertakan role dalam response
            'user' => $user
        ]);
    }
}