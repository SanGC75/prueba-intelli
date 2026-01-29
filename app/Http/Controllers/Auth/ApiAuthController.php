<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ApiAuthController extends Controller
{
    /**
     * Maneja el intento de autenticación y devuelve un token JWT.
     */
    public function login(Request $request)
    {
        // 1. Validar que lleguen los datos necesarios
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Extraer credenciales
        $credentials = $request->only('email', 'password');

        try {
            // 3. Intentar autenticar y generar el token
            // JWTAuth::attempt verifica el email y el hash del password automáticamente
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'error' => 'invalid_credentials',
                    'message' => 'El correo o la contraseña son incorrectos.'
                ], 401);
            }
        } catch (JWTException $e) {
            // Error si no se pudo crear el token por fallas técnicas
            return response()->json([
                'error' => 'could_not_create_token',
                'message' => 'No se pudo generar el token de seguridad.'
            ], 500);
        }

        // 4. Si todo sale bien, devolver el token generado
        return response()->json([
            'status' => 'success',
            'token' => $token
        ]);
    }

    /**
     * Opcional: Obtener los datos del usuario autenticado a través del token.
     */
    public function getAuthenticatedUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        return response()->json(compact('user'));
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'status' => 'success',
                'message' => 'Sesión cerrada correctamente'
            ], 200);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo cerrar la sesión, intente de nuevo'
            ], 500);
        }
    }

    public function me()
    {
        return response()->json(JWTAuth::parseToken()->authenticate());
    }
}