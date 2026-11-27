
<?php

require_once __DIR__ . '/../models/PedidoDAO.php';

class pedidoController
{
    public function confirmar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url . '?controller=carrito&action=index');
            exit;
        }

        // El cliente debe haber iniciado sesión.
        if (empty($_SESSION['usuario_id'])) {
            $_SESSION['error'] = 'Debes iniciar sesión para confirmar tu pedido.';

            header('Location: ' . url . '?controller=usuario&action=login');
            exit;
        }

        $carrito = $_SESSION['carrito'] ?? [];

        if (!is_array($carrito) || empty($carrito)) {
            $_SESSION['error'] = 'Tu carrito está vacío.';

            header('Location: ' . url . '?controller=carrito&action=index');
            exit;
        }

        try {
            $usuarioId = (int) $_SESSION['usuario_id'];
            $descuento = $_SESSION['descuento'] ?? null;

            $pedidoId = PedidoDAO::crearDesdeCarrito(
                $usuarioId,
                $carrito,
                $descuento
            );

            // Vaciar el carrito solamente después de guardar correctamente.
            $_SESSION['carrito'] = [];
            unset($_SESSION['descuento']);

            $_SESSION['success'] = 'Tu pedido se ha registrado correctamente. Número de pedido: ' . $pedidoId;
            $_SESSION['pedido_id'] = $pedidoId;

            header('Location: ' . url . '?controller=carrito&action=index');
            exit;

        } catch (InvalidArgumentException $e) {
            error_log('Error de validación del pedido: ' . $e->getMessage());

            $_SESSION['error'] = $e->getMessage();

            header('Location: ' . url . '?controller=carrito&action=index');
            exit;

        } catch (Throwable $e) {
            error_log('Error al crear el pedido: ' . $e->getMessage());

            $_SESSION['error'] = 'No se ha podido registrar el pedido. Comprueba tu carrito e inténtalo de nuevo.';

            header('Location: ' . url . '?controller=carrito&action=index');
            exit;
        }
    }
}
