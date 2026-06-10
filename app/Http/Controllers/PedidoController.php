<?php

namespace App\Http\Controllers;

use App\Events\NuevoPedidoRecibido;
use App\Events\StockBajoAlerta;
use App\Jobs\EnviarConfirmacionPedido;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'items'                   => 'required|array|min:1',
            'items.*.producto_id'     => 'required|exists:productos,id',
            'items.*.cantidad'        => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        $pedido = DB::transaction(function () use ($request) {
            $p = Pedido::create([
                'user_id' => auth()->id(),
                'total'   => collect($request->items)
                             ->sum(fn($i) => $i['precio_unitario'] * $i['cantidad']),
            ]);

            foreach ($request->items as $item) {
                $p->items()->create($item);

                $producto = Producto::find($item['producto_id']);
                $producto->decrement('stock', $item['cantidad']);
                $producto->refresh();

                if ($producto->stock <= 5) {
                    broadcast(new StockBajoAlerta($producto, $producto->stock));
                }
            }

            return $p;
        });

        EnviarConfirmacionPedido::dispatch($pedido)->delay(now()->addSeconds(5));
        broadcast(new NuevoPedidoRecibido($pedido));

        return response()->json(['pedido_id' => $pedido->id], 201);
    }

    public function show($id)
    {
        $pedido = Pedido::with('items')->findOrFail($id);
        return response()->json($pedido);
    }
}