<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *   path="/api/v1/register",
     *   tags={"Autenticación"},
     *   summary="Registrar nuevo usuario",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"name","email","password","password_confirmation"},
     *       @OA\Property(property="name",                  type="string",  example="Juan Pérez"),
     *       @OA\Property(property="email",                 type="string",  example="juan@test.com"),
     *       @OA\Property(property="password",              type="string",  example="password123"),
     *       @OA\Property(property="password_confirmation", type="string",  example="password123")
     *     )
     *   ),
     *   @OA\Response(response=201, description="Usuario creado"),
     *   @OA\Response(response=422, description="Datos inválidos")
     * )
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user], 201);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/login",
     *   tags={"Autenticación"},
     *   summary="Iniciar sesión",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email",    type="string", example="admin@test.com"),
     *       @OA\Property(property="password", type="string", example="password123")
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Login exitoso",
     *     @OA\JsonContent(
     *       @OA\Property(property="token", type="string"),
     *       @OA\Property(property="user",  type="object")
     *     )
     *   ),
     *   @OA\Response(response=401, description="Credenciales incorrectas")
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user]);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/logout",
     *   tags={"Autenticación"},
     *   summary="Cerrar sesión",
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(response=200, description="Sesión cerrada"),
     *   @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada']);
    }

    /**
     * @OA\Get(
     *   path="/api/v1/me",
     *   tags={"Autenticación"},
     *   summary="Obtener usuario autenticado",
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(response=200, description="Datos del usuario"),
     *   @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}