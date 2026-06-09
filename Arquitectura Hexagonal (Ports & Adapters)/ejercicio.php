<?php

// Puerto (interfaz del dominio)
interface NotificationPort {
    public function send(string $to, string $message): void;
}

// Adaptador Email
class EmailNotification implements NotificationPort {
    public function send(string $to, string $message): void {
        echo "📧 Email enviado a {$to}: {$message}\n";
    }
}

// Adaptador SMS
class SmsNotification implements NotificationPort {
    public function send(string $to, string $message): void {
        echo "📱 SMS enviado a {$to}: {$message}\n";
    }
}

// Adaptador Null (para pruebas)
class NullNotification implements NotificationPort {
    public function send(string $to, string $message): void {
        // No hace nada
    }
}

// Caso de uso del dominio
class SendWelcomeUseCase {
    public function __construct(
        private NotificationPort $notification
    ) {}

    public function execute(string $to): void {
        $message = "¡Bienvenido a nuestra plataforma!";
        $this->notification->send($to, $message);
    }
}

/* ==========================
   Prueba con Email
   ========================== */
echo "=== Adaptador Email ===\n";

$emailUseCase = new SendWelcomeUseCase(
    new EmailNotification()
);

$emailUseCase->execute("kevin@example.com");


/* ==========================
   Prueba con SMS
   ========================== */
echo "\n=== Adaptador SMS ===\n";

$smsUseCase = new SendWelcomeUseCase(
    new SmsNotification()
);

$smsUseCase->execute("+573001234567");


/* ==========================
   Prueba con Null
   ========================== */
echo "\n=== Adaptador Null ===\n";

$nullUseCase = new SendWelcomeUseCase(
    new NullNotification()
);

$nullUseCase->execute("test@example.com");

echo "Prueba completada sin enviar notificaciones.\n";

?>