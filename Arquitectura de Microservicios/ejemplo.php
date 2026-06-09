<?php

// order-service/src/OrderService.php
class OrderService {
    public function __construct(private HttpClient $http) {}

    public function createOrder(int $userId, array $items): array {
        // Llama al microservicio de usuarios
        $user = $this->http->get("http://user-service/api/users/{$userId}");

        if (!$user['active']) {
            throw new UnauthorizedException('Usuario inactivo');
        }

        $order = [
            'id'      => uniqid('order_'),
            'user_id' => $userId,
            'items'   => $items,
            'total'   => array_sum(array_column($items, 'price')),
            'status'  => 'pending',
        ];

        $this->save($order);

        // Publica evento para que el servicio de pagos lo procese
        $this->bus->publish('order.created', $order);

        return $order;
    }
}

?>