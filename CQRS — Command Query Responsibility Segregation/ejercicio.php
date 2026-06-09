<?php

// COMMAND BUS
class CommandBus {
    private array $handlers = [];

    // Registrar handlers
    public function register(string $commandClass, callable $handler): void {
        $this->handlers[$commandClass] = $handler;
    }

    // Despachar comandos
    public function dispatch(object $command): mixed {
        $commandClass = get_class($command);

        if (!isset($this->handlers[$commandClass])) {
            throw new Exception(
                "No existe un handler registrado para {$commandClass}"
            );
        }

        return ($this->handlers[$commandClass])($command);
    }
}

// ── COMMANDS ──────────────────────────────────────────

class CreateProductCommand {
    public function __construct(
        public readonly string $name,
        public readonly float $price
    ) {}
}

class UpdateProductCommand {
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly float $price
    ) {}
}

// ── HANDLERS ──────────────────────────────────────────

class CreateProductHandler {
    public function handle(CreateProductCommand $cmd): void {
        echo "✅ Producto creado: {$cmd->name} - $ {$cmd->price}\n";
    }
}

class UpdateProductHandler {
    public function handle(UpdateProductCommand $cmd): void {
        echo "✏️ Producto actualizado: {$cmd->id} | {$cmd->name} - $ {$cmd->price}\n";
    }
}

// ── CONFIGURACIÓN DEL BUS ─────────────────────────────

$bus = new CommandBus();

$createHandler = new CreateProductHandler();
$updateHandler = new UpdateProductHandler();

$bus->register(
    CreateProductCommand::class,
    fn(CreateProductCommand $cmd) =>
        $createHandler->handle($cmd)
);

$bus->register(
    UpdateProductCommand::class,
    fn(UpdateProductCommand $cmd) =>
        $updateHandler->handle($cmd)
);

// ── PRUEBAS ───────────────────────────────────────────

// Crear producto
$bus->dispatch(
    new CreateProductCommand(
        'Teclado Mecánico',
        49.99
    )
);

// Actualizar producto
$bus->dispatch(
    new UpdateProductCommand(
        'P001',
        'Teclado RGB',
        59.99
    )
);

// Prueba de excepción
try {

    $bus->dispatch(
        new class {}
    );

} catch (Exception $e) {

    echo "❌ Error: " . $e->getMessage();

}

?>