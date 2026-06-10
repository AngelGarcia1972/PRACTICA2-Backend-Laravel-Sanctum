<?php

namespace App\Events;

use App\Models\Pedido;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NuevoPedidoRecibido implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Pedido $pedido) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('admin-panel')];
    }

    public function broadcastWith(): array
    {
        return [
            'id'         => $this->pedido->id,
            'total'      => $this->pedido->total,
            'cliente'    => $this->pedido->user->name,
            'items'      => $this->pedido->items->count(),
            'created_at' => $this->pedido->created_at->format('H:i:s'),
        ];
    }
}