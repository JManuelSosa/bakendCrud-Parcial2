<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Helpers\ValidatorHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function UserIndex()
    {
        $users = User::orderBy('id', 'asc')->get();

        return response()->json(["data" => $users, "status" => 200], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ShowUser($id)
    {
        $user = User::find($id);

        if(!$user){
            return response()->json(['message' => 'Usuario no encontrado', "status" => 404], 404);
        }

        return response()->json(["data" => $user, "status" => 200], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function StoreUser(Request $request)
    {
        //* Usamos las validaciones de los datos con el validator
        $validator = ValidatorHelper::ValidateRegister($request->all(), "RegisterUser");

        if($validator->fails()){
            return response()->json([
                'message' => 'Hubo un error en los datos',
                'errors' => $validator->errors(),
                'status' => 422],
            422);
        }

        //* Creamos un nuevo usuario
        $newUser = User::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telefono' => $request->telefono,
            'ciudad' => $request->ciudad
        ]);

        //* Retornamos la respuesta
        return response()->json([
            'message' => 'Usuario creado exitosamente',
            'data'=> $newUser,
            'status' => 201
        ], 201);

    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function UpdateUser(Request $request, $id)
    {
        $user = User::find($id);

        if(!$user){
            return response()->json(['message' => 'Usuario no encontrado', 'status' => 404], 404);
        }

        $validator = ValidatorHelper::ValidateRegister($request->all(), 'UpdateUser', $id);

        if($validator->fails()){
            return response()->json(['message' => 'Ha introducido un dato incorrecto', 'errors' => $validator->errors()], 422);
        }

        //* Actualizamos todo
        ($request->has('nombre') && $request->nombre != null && $request->nombre != '') ? $user->nombre = $request->nombre : null;
        ($request->has('email') && $request->email != null && $request->email != '') ? $user->email = $request->email : null;
        ($request->has('password') && $request->password != null && $request->password != '') ? $user->password = Hash::make($request->password) : null;
        ($request->has('telefono') && $request->telefono != null && $request->telefono != '') ? $user->telefono = $request->telefono : null;
        ($request->has('ciudad') && $request->ciudad != null && $request->ciudad != '') ? $user->ciudad = $request->ciudad : null;

        $user->save();

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'data' => $user,
            'status' => 200
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function DestroyUser($id)
    {
        $usuarioAutenticado = auth()->user();

        // Verificar si el usuario autenticado intenta eliminarse a sí mismo
        if ($usuarioAutenticado->id == $id) {
            return response()->json([
                'message' => 'No puedes eliminarte a ti mismo',
                'status' => 403 // Código HTTP 403: Prohibido
            ], 403);
        }

        $user = User::find($id);
        $user->tokens()->delete();

        if(!$user){
            return response()->json(['message' => 'Usuario no encontrado', 'status' => 404], 404);
        }

        $user->delete();
        return response()->json(['message' => 'Usuario eliminado exitosamente', 'status' => 200], 200);
    }
}
