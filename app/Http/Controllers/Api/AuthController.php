<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @group Autentikasi
 */
class AuthController extends Controller
{
    /**
     * Login
     *
     * Menukar email dan password dengan token API.
     *
     * @unauthenticated
     *
     * @bodyParam email string required Email pengguna. Example: customer@example.com
     * @bodyParam password string required Password. Example: password
     *
     * @response 200 {
     *   "token": "1|abcdef123456...",
     *   "user": {"id": 3, "name": "Customer", "role": "customer"}
     * }
     * @response 422 {
     *   "message": "Email atau password salah.",
     *   "errors": {"email": ["Email atau password salah."]}
     * }
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        return response()->json([
            'token' => $user->createToken('api')->plainTextToken,
            'user'  => ['id' => $user->id, 'name' => $user->name, 'role' => $user->role],
        ]);
    }

    /**
     * Logout
     *
     * Mencabut token yang sedang dipakai.
     *
     * @authenticated
     *
     * @response 200 {"message": "Logout berhasil."}
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil.']);
    }
}