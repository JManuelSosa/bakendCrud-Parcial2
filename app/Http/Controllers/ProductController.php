<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Http\Helpers\ValidatorHelper;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function IndexProduct()
    {
        //* Obtenemos todos los productos de la DB
        $productos = Product::orderBy('id', 'asc')->get();

        return response()->json(["data" => $productos, "status" => 200], 200);
    }


    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ShowProduct($id)
    {
        $producto = Product::find($id);

        if(!$producto){
            return response()->json(["message" => "Producto no encontrado", "status" => 404], 404);
        }

        return response()->json(["data" => $producto, "status" => 200], 200);

    }

    /**
     * Store a newly created resource in storage.
     * Crear un nuevo producto
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function StoreProduct(Request $request)
    {
        $validator = ValidatorHelper::ValidateRegister($request->all(), "StoreProduct");

        if($validator->fails()){
            return response()->json(['message' => 'Ha introducido un dato incorrecto', 'errors' => $validator->errors(), 'status' => 422], 422);
        }


        $imagenPath = null;
        if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {

            $file = $request->file('imagen');
            $nombreImagen = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '-' . time() . '.' . $file->getClientOriginalExtension();
            $imagenPath = $file->storeAs('products', $nombreImagen, 'public');
        }

        $newProduct = Product::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => round($request->precio, 2),
            'stock' => $request->stock,
            'imagen' => ($imagenPath) ? asset('storage/' . $imagenPath) : null
        ]);

        return response()->json([
            "message" => "Producto creado exitosamente",
            "data" => $newProduct,
            "status" => 200
        ], 200);

    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id = Id del producto a actualizar
     * @return \Illuminate\Http\Response
     */
    public function UpdateProduct(Request $request, $id)
    {
        $producto = Product::find($id);

        if(!$producto){
            return response()->json(["message" => "Producto no encontrado", "status" => 404], 404);
        }

        $validator = ValidatorHelper::ValidateRegister($request->all(), 'UpdateProduct');

        if($validator->fails()){
            return response()->json(['message' => 'Ha introducido un dato incorrecto', 'errors' => $validator->errors(), 'status' => 422], 422);
        }

        // dd($request->file('imagen'));

        //* Actualizamos todo
        ($request->has('nombre') && $request->nombre != null && $request->nombre != '') ? $producto->nombre = $request->nombre : null;
        ($request->has('descripcion') && $request->descripcion != null && $request->descripcion != '') ? $producto->descripcion = $request->descripcion : null;
        ($request->has('precio') && $request->precio != null) ? $producto->precio = $request->precio : null;
        ($request->has('stock') && $request->stock != null) ? $producto->stock = $request->stock : null;

        $imagenPath = null;
        if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {


            if ($producto->imagen) {
                $nombreArchivo = basename($producto->imagen);
                $rutaImagen = "products/" . $nombreArchivo;
                Storage::disk('public')->delete($rutaImagen);
            }

            $file = $request->file('imagen');
            $nombreImagen = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '-' . time() . '.' . $file->getClientOriginalExtension();
            $imagenPath = $file->storeAs('products', $nombreImagen, 'public');

            $producto->imagen = asset('storage/' . $imagenPath);;
        }


        $producto->save();

        return response()->json([
            'message' => 'Producto actualizado correctamente',
            'data' => $producto,
            'status' => 200
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function DestroyProduct($id)
    {
        $producto = Product::find($id);

        if(!$producto){
            return response()->json(["message" => "Producto no encontrado", "status" => 404], 404);
        }

        if($producto->imagen){
            $rutaImagen = str_replace('/storage/', 'public/', $producto->imagen);
            Storage::delete($rutaImagen);
        }

        $producto->delete();
        return response()->json(['message' => 'Producto eliminado exitosamente', 'status' => 200], 200);
    }


}
