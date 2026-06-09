// Capa de Dominio
class User {
    public function __construct(
        public readonly int    $id,
        public readonly string $email,
        public readonly string $name
    ) {}
}

// Capa de Infraestructura
class UserRepository {
    public function findById(int $id): ?User {
        $row = $this->db->query("SELECT * FROM users WHERE id = $id")->fetch();
        return $row ? new User($row['id'], $row['email'], $row['name']) : null;
    }
}

// Capa de Aplicación
class GetUserUseCase {
    public function __construct(private UserRepository $repo) {}

    public function execute(int $id): User {
        $user = $this->repo->findById($id);
        if (!$user) throw new NotFoundException("User $id not found");
        return $user;
    }
}

// Capa de Presentación
class UserController {
    public function show(int $id): array {
        $user = (new GetUserUseCase(new UserRepository()))->execute($id);
        return ['id' => $user->id, 'name' => $user->name];
    }
}