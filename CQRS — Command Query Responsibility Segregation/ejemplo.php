<?php

// ── COMMAND SIDE ──────────────────────────────────────

class CreateProductCommand {
    public function __construct(
        public readonly string $name,
        public readonly float  $price
    ) {}
}

class CreateProductHandler {
    public function handle(CreateProductCommand $cmd): void {
        // Valida, crea entidad, persiste en Write DB
        $product = new Product(uniqid(), $cmd->name, $cmd->price);
        $this->writeDb->save($product);
        $this->bus->publish('product.created', $product);
    }
}

// ── QUERY SIDE ────────────────────────────────────────

class GetProductsQuery {
    public function __construct(public readonly int $limit = 20) {}
}

class GetProductsHandler {
    public function handle(GetProductsQuery $query): array {
        // Lee directo del Read DB (puede ser una vista desnormalizada)
        return $this->readDb->query(
            "SELECT id, name, price FROM products_view LIMIT {$query->limit}"
        )->fetchAll();
    }
}

// ── USO ───────────────────────────────────────────────
$bus->dispatch(new CreateProductCommand('Teclado', 49.99));
$products = $bus->dispatch(new GetProductsQuery(10));

?>