<?php

class EventBus {
    private array $listeners = [];
    private array $onceListeners = [];

    // Suscribir listener normal
    public function subscribe(string $event, callable $handler): void {
        $this->listeners[$event][] = $handler;
    }

    // Suscribir listener que se ejecuta una sola vez
    public function subscribeOnce(string $event, callable $handler): void {
        $this->onceListeners[$event][] = $handler;
    }

    // Eliminar un listener específico
    public function unsubscribe(string $event, callable $handler): void {
        if (!isset($this->listeners[$event])) {
            return;
        }

        foreach ($this->listeners[$event] as $key => $listener) {
            if ($listener === $handler) {
                unset($this->listeners[$event][$key]);
            }
        }
    }

    // Publicar evento
    public function publish(string $event, array $payload): void {

        // Ejecutar listeners normales
        foreach ($this->listeners[$event] ?? [] as $handler) {
            $handler($payload);
        }

        // Ejecutar listeners de una sola vez
        foreach ($this->onceListeners[$event] ?? [] as $handler) {
            $handler($payload);
        }

        // Eliminar listeners de una sola ejecución
        unset($this->onceListeners[$event]);
    }
}

$bus = new EventBus();

/* Listener 1: Enviar email de bienvenida */
$emailListener = function(array $user) {
    echo "📧 Bienvenida enviada a {$user['email']}\n";
};

$bus->subscribe('user.registered', $emailListener);

/* Listener 2: Crear perfil en la base de datos */
$bus->subscribe('user.registered', function(array $user) {
    echo "👤 Perfil creado para {$user['name']}\n";
});

/* Listener 3: Registrar en Analytics */
$bus->subscribe('user.registered', function(array $user) {
    echo "📊 Usuario registrado en Analytics: {$user['id']}\n";
});

/* Listener que se ejecuta una sola vez */
$bus->subscribeOnce('user.registered', function(array $user) {
    echo "🎁 Bono de bienvenida otorgado a {$user['name']}\n";
});

/* Publicación del evento */
$bus->publish('user.registered', [
    'id'    => 1,
    'email' => 'kevin@example.com',
    'name'  => 'Kevin Ramos'
]);

echo "\n--- Segunda publicación ---\n";

/* El listener subscribeOnce ya no se ejecutará */
$bus->publish('user.registered', [
    'id'    => 2,
    'email' => 'ana@example.com',
    'name'  => 'Ana Torres'
]);

/* Eliminar listener de correo */
$bus->unsubscribe('user.registered', $emailListener);

echo "\n--- Tercera publicación (sin email) ---\n";

$bus->publish('user.registered', [
    'id'    => 3,
    'email' => 'carlos@example.com',
    'name'  => 'Carlos Pérez'
]);

?>