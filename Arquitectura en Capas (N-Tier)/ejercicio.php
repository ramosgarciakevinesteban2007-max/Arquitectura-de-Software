// ── DOMINIO ────────────────────────────────────────────
// Igual que User: entidad pura, sin dependencias externas
class Product {
    public function __construct(
        public readonly int    $id,
        public readonly string $name,
        public readonly float  $price
    ) {}
}

// ── INFRAESTRUCTURA ────────────────────────────────────
// Implementación concreta: PDO + MySQL
class ProductRepository {
    public function __construct(private PDO $db) {}

    public function findAll(): array {
        $rows = $this->db->query('SELECT * FROM products')->fetchAll();
        return array_map(
            fn($r) => new Product($r['id'], $r['name'], (float)$r['price']),
            $rows
        );
    }

    public function save(Product $p): void {
        $stmt = $this->db->prepare(
            'INSERT INTO products (name, price) VALUES (?, ?)'
        );
        $stmt->execute([$p->name, $p->price]);
        }
}    

// ── APLICACIÓN ─────────────────────────────────────────
// Contiene la regla de negocio: precio > 0
class CreateProductUseCase {
    public function __construct(private ProductRepository $repo) {}

    public function execute(string $name, float $price): Product {
        if ($price <= 0) {
            throw new \InvalidArgumentException('El precio debe ser mayor a 0.');
        }

        // id=0: la DB asignará el ID real al insertar
        $product = new Product(0, $name, $price);
        $this->repo->save($product);
        return $product;
    }
}

// ── PRESENTACIÓN ───────────────────────────────────────
// Solo coordina: recibe datos HTTP, llama al use case, devuelve respuesta
class ProductController {
    public function store(array $data): array {
        $useCase = new CreateProductUseCase(
            new ProductRepository(new PDO('mysql:host=localhost;dbname=shop', 'u', 'p'))
        );
        $product = $useCase->execute($data['name'], (float)$data['price']);
        return [
            'id'    => $product->id,
            'name'  => $product->name,
            'price' => $product->price,
        ];
    }
}

// ── Uso ────────────────────────────────────────────────
$ctrl = new ProductController();
$response = $ctrl->store(['name' => 'Teclado mecánico', 'price' => 89.99]);
// ['id' => 0, 'name' => 'Teclado mecánico', 'price' => 89.99]