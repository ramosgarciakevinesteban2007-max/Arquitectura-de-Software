<?php

// Puerto (interfaz del dominio — no depende de nada externo)
interface UserRepositoryPort {
    public function findById(int $id): ?User;
    public function save(User $user): void;
}

// Adaptador MySQL (infraestructura)
class MysqlUserRepository implements UserRepositoryPort {
    public function findById(int $id): ?User {
        // ... consulta real a MySQL
    }
    public function save(User $user): void { /* ... */ }
}

// Adaptador en memoria (para tests — sin DB real)
class InMemoryUserRepository implements UserRepositoryPort {
    private array $store = [];
    public function findById(int $id): ?User {
        return $this->store[$id] ?? null;
    }
    public function save(User $user): void {
        $this->store[$user->id] = $user;
    }
}

// Use case del dominio — solo conoce el puerto, no el adaptador
class RegisterUserUseCase {
    public function __construct(private UserRepositoryPort $repo) {}

    public function execute(string $email, string $name): User {
        $user = new User(rand(1, 9999), $email, $name);
        $this->repo->save($user);
        return $user;
    }
}

// Producción usa MySQL; tests usan InMemory — mismo use case
$useCase = new RegisterUserUseCase(new MysqlUserRepository());

?>