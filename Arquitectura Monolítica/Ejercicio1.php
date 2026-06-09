// index.php — monolito extendido (mismo archivo)

class BlogApp {
    private PDO $db;

    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;dbname=blog', 'user', 'pass');
    }


    // UI: renderiza HTML
    public function render($posts): void {
        foreach ($posts as $post) {
            echo "<h2>{$post['title']}</h2>";
        }
    }

// Lógica de negocio: filtra publicados
    public function getPublishedPosts(): array {
        $posts = $this->fetchFromDb();
        return array_filter($posts, fn($p) => $p['status'] === 'published');
    }


    // ── VALIDACIÓN (nueva) ───────────────────────────────
    private function validateTitle(string $title): void {
        if (trim($title) === '') {
            throw new \InvalidArgumentException('El título no puede estar vacío.');
        }
        if (strlen($title) >= 100) {
            throw new \InvalidArgumentException('El título debe tener menos de 100 caracteres.');
        }
    }

     // ── NUEVO: createPost ────────────────────────────────
    public function createPost(string $title, string $body): void {
        $this->validateTitle($title);           // validación acoplada aquí

        $stmt = $this->db->prepare(
            'INSERT INTO posts (title, body, status) VALUES (?, ?, ?)'
        );
        $stmt->execute([$title, $body, 'draft']);
    }


    // ── NUEVO: deletePost ────────────────────────────────
    public function deletePost(int $id): void {
        $stmt = $this->db->prepare('DELETE FROM posts WHERE id = ?');
        $stmt->execute([$id]);
    }

    // Acceso a datos
    private function fetchFromDb(): array {
        return $this->db->query('SELECT * FROM posts')->fetchAll();
    }

}

// ── Uso ──────────────────────────────────────────────
$app = new BlogApp();

// Mostrar posts publicados
$app->render($app->getPublishedPosts());

// Crear un post nuevo
$app->createPost('Mi primer post', 'Contenido del post...');

// Eliminar un post por ID
$app->deletePost(3);
