<?php

namespace App\Events;

use App\Models\Producto;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockBajoAlerta implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Producto $producto,
        public int $stockActual
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('admin-panel')];
    }

    public function broadcastWith(): array
    {
        return [
            'producto_id'  => $this->producto->id,
            'nombre'       => $this->producto->nombre,
            'stock_actual' => $this->stockActual,
        ];
    }
}