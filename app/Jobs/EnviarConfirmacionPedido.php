<?php

namespace App\Jobs;

use App\Models\Pedido;
use App\Notifications\ConfirmacionPedidoNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class EnviarConfirmacionPedido implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(public Pedido $pedido) {}

    public function handle(): void
    {
        $this->pedido->user->notify(
            new ConfirmacionPedidoNotification($this->pedido)
        );

        $this->pedido->update([
            'email_enviado_at' => now()->toDateTimeString()
        ]);
    }

    public function failed(Throwable $e): void
    {
        Log::error('Fallo envío email pedido ' . $this->pedido->id);
    }
}