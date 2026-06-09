// Model
class PostModel {
    public function all(): array {
        return [
            ['id' => 1, 'title' => 'Primer post', 'body' => 'Contenido...'],
            ['id' => 2, 'title' => 'Segundo post', 'body' => 'Más contenido...'],
        ];
    }
}

// View
class PostView {
    public function renderList(array $posts): string {
        $html = '<ul>';
        foreach ($posts as $post) {
            $html .= "<li><strong>{$post['title']}</strong></li>";
        }
        return $html . '</ul>';
    }
}

// Controller
class PostController {
    public function index(): string {
        $model = new PostModel();
        $view  = new PostView();
        $posts = $model->all();
        return $view->renderList($posts);
    }
}

echo (new PostController())->index();