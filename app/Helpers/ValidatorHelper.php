<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Validator;

class ValidatorHelper
{
    /**
    * * Función de validación de nuevos registros y sus reglas
    * @param $data = Los parámetros que se reciben en el post de registro, pueden tener diversas reglas
    * @param $type = El tipo o el controlador donde se colocan los parámetros de validación.
    */

    public static function ValidateRegister($data, $type, $idUser = null)
    {

        $rules = [

            "RegisterUser" => [
                'nombre' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'telefono' => 'required|string|min:10|max:10|regex:/^[0-9]+$/',
                'ciudad' => 'required|string|max:100'
            ],

            "UpdateUser" => [
                'nombre' => 'string|max:255',
                'email' => 'string|email|max:255|unique:users,email,' . $idUser,
                'password' => 'string|min:8',
                'telefono' => 'string|min:10|max:10|regex:/^[0-9]+$/',
                'ciudad' => 'string|max:100'
            ],

            "StoreProduct" => [
                "nombre" => "required|string|max:100",
                "descripcion" => "required|string|max:800",
                "precio" => "required|numeric|min:0|max:99999999.99",
                "stock" => "required|integer|min:0",
                "imagen" => "nullable|image|mimes:jpg,png,jpeg|max:5120"
            ],

            "UpdateProduct" => [
                "nombre" => "string|max:100",
                "descripcion" => "string|max:500",
                "precio" => "numeric|min:0|max:99999999.99",
                "stock" => "integer|min:0",
                "imagen" => "nullable|image|mimes:jpg,png,jpeg|max:5120"
            ]


        ];

        return Validator::make($data, $rules[$type]);
    }
}
