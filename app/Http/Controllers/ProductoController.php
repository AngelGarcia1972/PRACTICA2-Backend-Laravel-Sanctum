<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use App\Http\Resources\ProductoResource;

class ProductoController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/v1/productos",
     *   tags={"Productos"},
     *   summary="Listar todos los productos",
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(response=200, description="Lista de productos"),
     *   @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function index(Request $request)
    {
        return ProductoResource::collection(Producto::all());
    }

    /**
     * @OA\Get(
     *   path="/api/v1/productos/{id}",
     *   tags={"Productos"},
     *   summary="Obtener un producto por ID",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Producto encontrado"),
     *   @OA\Response(response=404, description="No encontrado"),
     *   @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function show($id)
    {
        $producto = Producto::find($id);
        if (!$producto) {
            return response()->json(['mensaje' => 'Producto no encontrado'], 404);
        }
        return new ProductoResource($producto);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/productos",
     *   tags={"Productos"},
     *   summary="Crear un producto",
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"nombre","precio","stock"},
     *       @OA\Property(property="nombre",  type="string",  example="Laptop HP"),
     *       @OA\Property(property="precio",  type="number",  example=9999.99),
     *       @OA\Property(property="stock",   type="integer", example=10)
     *     )
     *   ),
     *   @OA\Response(response=201, description="Producto creado"),
     *   @OA\Response(response=422, description="Datos inválidos"),
     *   @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'precio' => 'required|numeric',
            'stock'  => 'required|integer',
            'imagen' => 'nullable|image|mimes:jpg,png,webp|max:2048',
        ]);

        $data = $request->except('imagen');

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')
                ->store('productos', 'public');
        }

        return new ProductoResource(Producto::create($data));
    }

    /**
     * @OA\Put(
     *   path="/api/v1/productos/{id}",
     *   tags={"Productos"},
     *   summary="Actualizar un producto",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       @OA\Property(property="nombre",  type="string",  example="Laptop Dell"),
     *       @OA\Property(property="precio",  type="number",  example=12999.99),
     *       @OA\Property(property="stock",   type="integer", example=5)
     *     )
     *   ),
     *   @OA\Response(response=200, description="Producto actualizado"),
     *   @OA\Response(response=404, description="No encontrado"),
     *   @OA\Response(response=422, description="Datos inválidos"),
     *   @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function update(Request $request, $id)
    {
        $producto = Producto::find($id);
        if (!$producto) {
            return response()->json(['mensaje' => 'Producto no encontrado'], 404);
        }

        $data = $request->except('imagen');

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')
                ->store('productos', 'public');
        }

        $producto->update($data);
        return new ProductoResource($producto);
    }

    /**
     * @OA\Delete(
     *   path="/api/v1/productos/{id}",
     *   tags={"Productos"},
     *   summary="Eliminar un producto",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=204, description="Eliminado"),
     *   @OA\Response(response=404, description="No encontrado"),
     *   @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function destroy($id)
    {
        $producto = Producto::find($id);
        if (!$producto) {
            return response()->json(['mensaje' => 'Producto no encontrado'], 404);
        }
        $producto->delete();
        return response()->json(null, 204);
    }
}