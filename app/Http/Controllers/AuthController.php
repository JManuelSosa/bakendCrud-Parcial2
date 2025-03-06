<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Helpers\ValidatorHelper;
use App\Models\User;


class AuthController extends Controller
{

    public function RegisterUser(Request $request){

        //* Usamos las validaciones de los datos con el validator
        $validator = ValidatorHelper::ValidateRegister($request->all(), "RegisterUser");

        //* Retornamos el error en caso de fallos
        if($validator->fails()){
            return response()->json(['message' => 'Hubo un error en los datos', 'errors' => $validator->errors(), 'status' => 422], 422);
        }

        //* Creamos el nuevo usuario con los datos recibidos
        $newUser = User::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telefono' => $request->telefono,
            'ciudad' => $request->ciudad
        ]);

        //* Generamos su token
        // $token = $newUser->createToken('auth_token')->plainTextToken;

        //* Retornamos la respuesta
        return response()->json([
            'message' => 'Usuario creado exitosamente',
            'data'=> $newUser,
            'status' => 201
        ], 201);

    }


    public function Login(Request $request){

        //* Validamos las credenciales de acceso
        if(!Auth::attempt($request->only('email', 'password'))){

            return response()->json(['message' => 'Usuario o contraseña incorrectos', 'status' => 401], 401);
        }

        //* Generamos usuario y token
        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            "message" => "Bienvenido $user->nombre",
            "accessToken" => $token,
            "tokenType" => "Bearer",
            "user" => $user,
            "statys" => 200
        ], 200);

    }

    public function Logout(Request $request){

        //* Borramos todos los tokens de acceso de la BD
        Auth::user()->tokens()->delete();
        return response()->json(['message' => 'Se ha cerrado su sesión correctamente', 'status' => 200], 200);

    }



}
