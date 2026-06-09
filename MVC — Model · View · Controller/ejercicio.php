<?php

// MODEL
class TaskModel {
    private array $tasks = [
        ['id' => 1, 'title' => 'Estudiar MVC', 'done' => false],
        ['id' => 2, 'title' => 'Realizar ejercicio', 'done' => true],
    ];

    public function all(): array {
        return $this->tasks;
    }

    public function create(array $data): void {
        $this->tasks[] = [
            'id'    => count($this->tasks) + 1,
            'title' => $data['title'],
            'done'  => $data['done'] ?? false
        ];
    }

    public function update(int $id, array $data): void {
        foreach ($this->tasks as &$task) {
            if ($task['id'] === $id) {
                $task['title'] = $data['title'] ?? $task['title'];
                $task['done']  = $data['done'] ?? $task['done'];
            }
        }
    }

    public function delete(int $id): void {
        foreach ($this->tasks as $key => $task) {
            if ($task['id'] === $id) {
                unset($this->tasks[$key]);
            }
        }
    }
}

// VIEW
class TaskView {

    // Vista de listado
    public function renderList(array $tasks): string {
        $html = "<h2>Lista de Tareas</h2><ul>";

        foreach ($tasks as $task) {
            $status = $task['done'] ? '✓' : '✗';
            $html .= "<li>{$status} {$task['title']}</li>";
        }

        $html .= "</ul>";

        return $html;
    }

    // Vista del formulario
    public function renderForm(): string {
        return '
            <h2>Nueva Tarea</h2>
            <form>
                <input type="text" name="title" placeholder="Título">
                <button type="submit">Guardar</button>
            </form>
        ';
    }
}

// CONTROLLER
class TaskController {

    private TaskModel $model;
    private TaskView $view;

    public function __construct() {
        $this->model = new TaskModel();
        $this->view  = new TaskView();
    }

    // Mostrar tareas
    public function index(): string {
        return $this->view->renderList(
            $this->model->all()
        );
    }

    // Crear tarea
    public function store(array $data): string {

        if (empty(trim($data['title'] ?? ''))) {
            return "❌ Error: El título no puede estar vacío.";
        }

        $this->model->create($data);

        return "✅ Tarea creada correctamente.";
    }

    // Actualizar tarea
    public function update(int $id, array $data): string {

        if (isset($data['title']) && empty(trim($data['title']))) {
            return "❌ Error: El título no puede estar vacío.";
        }

        $this->model->update($id, $data);

        return "✅ Tarea actualizada correctamente.";
    }

    // Eliminar tarea
    public function destroy(int $id): string {
        $this->model->delete($id);

        return "🗑️ Tarea eliminada correctamente.";
    }

    // Mostrar formulario
    public function createForm(): string {
        return $this->view->renderForm();
    }
}

/* ==========================
   PRUEBAS DEL MVC
   ========================== */

$controller = new TaskController();

echo $controller->index();

echo "<hr>";

echo $controller->createForm();

echo "<hr>";

echo $controller->store([
    'title' => 'Aprender Arquitectura MVC',
    'done'  => false
]);

echo "<br>";

echo $controller->update(1, [
    'title' => 'Aprender MVC Avanzado',
    'done'  => true
]);

echo "<br>";

echo $controller->destroy(2);

?>