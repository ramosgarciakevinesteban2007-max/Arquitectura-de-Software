<?php

// index.php  — todo en un solo archivo/proyecto

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

    // Lógica de negocio
    public function getPublishedPosts(): array {
        $posts = $this->fetchFromDb();
        return array_filter($posts, fn($p) => $p['status'] === 'published');
    }

    // Acceso a datos
    private function fetchFromDb(): array {
        return $this->db->query('SELECT * FROM posts')->fetchAll();
    }
}

$app = new BlogApp();
$app->render($app->getPublishedPosts());

?>