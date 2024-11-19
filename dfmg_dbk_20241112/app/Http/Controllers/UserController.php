<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function create(Request $request)
    {
        // Validación de los datos recibidos
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:8|max:20',
        ]);

        // Error por si la validación falla
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'code' => 422,
                    'msg' => 'Errores de validación',
                    'details' => $validator->errors()
                ],
                'msg' => 'Datos de entrada inválidos',
                'count' => 0
            ], 422);
        }

        // Creación el usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Encriptar la contraseña
        ]);

        // Generar el token
        $accessToken = $user->createToken('API Token')->accessToken;

        // Retornar la respuesta
        return response()->json([
            'success' => true,
            'errors' => null,
            'data' => [
                'access_token' => $accessToken,
                'token_type' => 'bearer',
            ],
            'msg' => 'Usuario creado con éxito',
            'count' => 1
        ], 201);
    }

    // FUNCIÓN LOGIN
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'code' => 422,
                    'msg' => 'Errores de validación',
                    'details' => $validator->errors()
                ],
                'msg' => 'Datos de entrada inválidos',
                'count' => 0
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'code' => 401,
                    'msg' => 'Credenciales incorrectas'
                ],
                'msg' => 'No se reconocen las credenciales',
                'count' => 0
            ], 401);
        }

        $accessToken = $user->createToken('API Token')->accessToken;

        return response()->json([
            'success' => true,
            'errors' => null,
            'data' => [
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
            ],
            'msg' => 'Inicio de sesión exitoso',
            'count' => 1
        ], 200);
    }

    public function getUser(Request $request)
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Verificar si hay un usuario autenticado
        if (!$user) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'code' => 401,
                    'msg' => 'No autenticado'
                ],
                'msg' => 'No se pudo autenticar al usuario',
                'count' => 0
            ], 401);
        }

        // Responder con los detalles del usuario
        return response()->json([
            'success' => true,
            'errors' => null,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
            'msg' => 'Detalles del usuario obtenidos con éxito',
            'count' => 1
        ], 200);
    }


}

?>
