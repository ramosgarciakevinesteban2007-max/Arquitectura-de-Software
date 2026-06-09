<?php

class EventBus {
    private array $listeners = [];

    public function subscribe(string $event, callable $handler): void {
        $this->listeners[$event][] = $handler;
    }

    public function publish(string $event, array $payload): void {
        foreach ($this->listeners[$event] ?? [] as $handler) {
            $handler($payload);
        }
    }
}

// Registro de listeners (consumidores)
$bus = new EventBus();

$bus->subscribe('order.created', function(array $order) {
    echo "💳 Pagos: procesando orden {$order['id']}\n";
});

$bus->subscribe('order.created', function(array $order) {
    echo "📧 Email: enviando confirmación a {$order['user_email']}\n";
});

// El productor solo publica — no sabe quién escucha
$bus->publish('order.created', [
    'id'         => 'order_abc',
    'user_email' => 'user@example.com',
    'total'      => 99.90,
]);

?>