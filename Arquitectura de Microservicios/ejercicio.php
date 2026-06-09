// api-gateway/src/ApiGateway.php

class ApiGateway
{
    private array $services = [
        'users'    => 'http://user-service/api',
        'orders'   => 'http://order-service/api',
        'payments' => 'http://payment-service/api',
    ];

    public function __construct(
        private HttpClient $http,
        private Logger $logger
    ) {}

    public function handleRequest(
        string $method,
        string $service,
        string $endpoint
    ): array {

        $start = microtime(true);

        try {

            if (!isset($this->services[$service])) {
                throw new Exception('Servicio no encontrado');
            }

            $url = $this->services[$service] . $endpoint;

            // Timeout de 3 segundos
            $response = $this->http->request(
                $method,
                $url,
                ['timeout' => 3]
            );

            $responseTime = round(
                (microtime(true) - $start) * 1000,
                2
            );

            // Registro de log
            $this->logger->info([
                'method' => $method,
                'service' => $service,
                'response_time_ms' => $responseTime
            ]);

            return $response;

        } catch (Exception $e) {

            $responseTime = round(
                (microtime(true) - $start) * 1000,
                2
            );

            // Registro de error
            $this->logger->error([
                'method' => $method,
                'service' => $service,
                'response_time_ms' => $responseTime,
                'error' => $e->getMessage()
            ]);

            return [
                'status' => 'error',
                'message' => 'Servicio no disponible'
            ];
        }
    }
}