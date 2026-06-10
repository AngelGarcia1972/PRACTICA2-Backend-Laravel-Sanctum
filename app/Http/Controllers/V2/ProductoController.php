<?php

namespace App\Http\Controllers\V2;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductoResource;
use App\Models\Producto;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="V2 - Productos", description="Productos con búsqueda full-text (v2)")
 */
class ProductoController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/v2/productos",
     *   tags={"V2 - Productos"},
     *   summary="Listar productos con búsqueda",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="q",
     *     in="query",
     *     description="Texto a buscar en nombre y descripción",
     *     required=false,
     *     @OA\Schema(type="string", example="laptop")
     *   ),
     *   @OA\Response(response=200, description="Lista de productos"),
     *   @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function index(Request $request)
    {
        $query = Producto::query();
        if ($request->q) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', "%{$request->q}%")
                  ->orWhere('descripcion', 'like', "%{$request->q}%");
            });
        }
        return ProductoResource::collection($query->paginate(20));
    }
}
