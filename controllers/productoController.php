
<?php
//llamamos a los modelos que vamos a usar
require_once __DIR__ . '/../models/ProductoDAO.php';
require_once __DIR__ . '/../models/IngredienteDAO.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Plato.php';
require_once __DIR__ . '/../models/Bebida.php';
require_once __DIR__ . '/../models/Postre.php';

class productoController {

//API
//ejemplo de api clase magistral 18/11/26 David Navarro
//funcion API
    
public function api()
{
    header("Content-Type: application/json; charset=UTF-8");

    $metodo = $_SERVER['REQUEST_METHOD'];
    $funcion = $_GET['funcion'] ?? '';

    try {

        // OBTENER TODOS LOS PRODUCTOS
        if ($metodo === "GET" && $funcion === "todos") {

            $productos = ProductoDAO::getAllJson();
            $resultado = [];

            foreach ($productos as $producto) {
                $resultado[] = [
                    'id' => $producto->getId(),
                    'categoria_id' => $producto->getCategoriaId(),
                    'nombre' => $producto->getNombre(),
                    'descripcion' => $producto->getDescripcion(),
                    'precio' => $producto->getPrecio(),
                    'imagen' => $producto->getImagen(),
                    'activo' => $producto->getActivo(),
                    'tipo' => $producto->getTipo()
                ];
            }

            echo json_encode([
                'success' => true,
                'productos' => $resultado
            ]);

            exit;
        }

        // ELIMINAR UN PRODUCTO
        if ($metodo === "DELETE" && $funcion === "eliminar") {

            $datos = json_decode(file_get_contents("php://input"), true);

            if (!isset($datos['id']) || !filter_var($datos['id'], FILTER_VALIDATE_INT) || $datos['id'] <= 0) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'mensaje' => 'ID de producto no válido'
                ]);
                exit;
            }

            // Comprueba aquí que el usuario tiene permisos de administrador.
            if (!$this->esAdministrador()) {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'mensaje' => 'No tienes permisos para eliminar productos'
                ]);
                exit;
            }

            $id = (int) $datos['id'];
            $producto = ProductoDAO::getById($id);

            if (!$producto) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'mensaje' => 'El producto no existe'
                ]);
                exit;
            }

            $eliminado = ProductoDAO::delete($id);

            echo json_encode([
                'success' => $eliminado,
                'mensaje' => $eliminado
                    ? 'Producto eliminado correctamente'
                    : 'No se pudo eliminar el producto'
            ]);

            exit;
        }

        // PETICIÓN NO RECONOCIDA
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'mensaje' => 'Método o función no válida'
        ]);

        exit;

    } catch (Throwable $e) {

        http_response_code(500);

        // El error detallado se registra en el servidor, no se muestra al usuario.
        error_log($e->getMessage());

        echo json_encode([
            'success' => false,
            'mensaje' => 'Se ha producido un error en el servidor'
        ]);

        exit;
    }
}

//FIN de la api clase magistral 18/11/26 David Navarro

    
    // Muestra la pantalla de personalización.

    public function personalizar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id || $id <= 0) {
            $_SESSION['error'] = 'Producto no válido.';
            header('Location: ' . url . '?controller=carta&action=index');
            exit;
        }

        $producto = ProductoDAO::getById($id);

        if (!$producto || !$producto->getActivo()) {
            $_SESSION['error'] = 'El producto no está disponible.';
            header('Location: ' . url . '?controller=carta&action=index');
            exit;
        }

        $ingredientesProducto = [];
        $todosIngredientes = [];

        if ($producto instanceof Plato) {
            $ingredientesProducto = IngredienteDAO::getByProductoId($id);
            $todosIngredientes = IngredienteDAO::getAll();
        }

        require_once __DIR__ . '/../views/productos/personalizar.php';
    }

    /*
    Añade un producto al carrito.
    Los platos pueden personalizarse.
    En modo directo conservan todos sus ingredientes originales.
    Las bebidas y los postres se añaden directamente.
     */
    public function agregarCarrito()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Solo se permiten peticiones POST.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url . '?controller=carta&action=index');
            exit;
        }

        $productoId = filter_input(
            INPUT_POST,
            'producto_id',
            FILTER_VALIDATE_INT
        );

        if (!$productoId || $productoId <= 0) {
            $_SESSION['error'] = 'El producto seleccionado no es válido.';
            header('Location: ' . url . '?controller=carta&action=index');
            exit;
        }

        // Buscar el producto.
        $producto = ProductoDAO::getById($productoId);

        if (!$producto || !$producto->getActivo()) {
            $_SESSION['error'] = 'El producto no está disponible.';
            header('Location: ' . url . '?controller=carta&action=index');
            exit;
        }

        // Crear el carrito si todavía no existe.
        if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        $precioBase = (float) $producto->getPrecio();
        $precioFinal = $precioBase;

        $ingredientesSeleccionados = [];
        $extrasSeleccionados = [];

        // BEBIDAS Y POSTRES
        // No permiten modificaciones.
        if (!$producto instanceof Plato) {

            $_SESSION['carrito'][] = [
                'producto_id' => (int) $producto->getId(),
                'nombre' => $producto->getNombre(),
                'precio' => $precioBase,
                'cantidad' => 1,
                'ingredientes' => [],
                'extras' => [],
                'subtotal' => $precioBase
            ];

            $_SESSION['success'] = 'Producto añadido al carrito.';

            header('Location: ' . url . '?controller=carta&action=index');
            exit;
        }

        // PLATOS
        $originales = IngredienteDAO::getByProductoId($productoId);

        $modoDirecto = ($_POST['modo'] ?? '') === 'directo';

        if ($modoDirecto) {

            // Añadir todos los ingredientes originales.
            foreach ($originales as $ingrediente) {
                $ingredientesSeleccionados[] = (int) $ingrediente->id;
            }

            $extrasSeleccionados = [];

        } else {

            // Ingredientes originales elegidos por el usuario.
            $seleccionados = $_POST['ingredientes'] ?? [];

            if (!is_array($seleccionados)) {
                $seleccionados = [];
            }

            $idsOriginales = array_map(
                fn($ingrediente) => (int) $ingrediente->id,
                $originales
            );

            foreach ($seleccionados as $idIngrediente) {
                $idIngrediente = filter_var(
                    $idIngrediente,
                    FILTER_VALIDATE_INT
                );

                if (
                    $idIngrediente &&
                    in_array($idIngrediente, $idsOriginales, true)
                ) {
                    $ingredientesSeleccionados[] = $idIngrediente;
                }
            }

            // Evitar ingredientes duplicados.
            $ingredientesSeleccionados = array_unique(
                $ingredientesSeleccionados
            );

            // Extras seleccionados.
            $extras = $_POST['extras'] ?? [];

            if (!is_array($extras)) {
                $extras = [];
            }

            $extras = array_unique($extras);

            // Maximo de cuatro extras.
            $extras = array_slice($extras, 0, 4);

            // Obtener ingredientes activos disponibles.
            $ingredientesDisponibles = IngredienteDAO::getAll();

            $idsOriginales = array_map(
                fn($ingrediente) => (int) $ingrediente->id,
                $originales
            );

            foreach ($extras as $idExtra) {

                $idExtra = filter_var(
                    $idExtra,
                    FILTER_VALIDATE_INT
                );

                if (!$idExtra || in_array($idExtra, $idsOriginales, true)) {
                    continue;
                }

                foreach ($ingredientesDisponibles as $ingrediente) {

                    if ((int) $ingrediente->id === $idExtra) {

                        $extrasSeleccionados[] = [
                            'id' => (int) $ingrediente->id,
                            'nombre' => $ingrediente->nombre,
                            'precio' => (float) $ingrediente->precio_extra
                        ];

                        $precioFinal += (float) $ingrediente->precio_extra;

                        break;
                    }
                }
            }
        }

        // Añadir el plato al carrito.
        $_SESSION['carrito'][] = [
            'producto_id' => (int) $producto->getId(),
            'nombre' => $producto->getNombre(),
            'precio' => $precioBase,
            'cantidad' => 1,
            'ingredientes' => array_values($ingredientesSeleccionados),
            'extras' => $extrasSeleccionados,
            'subtotal' => $precioFinal
        ];

        $_SESSION['success'] = 'Producto añadido al carrito correctamente.';

        header('Location: ' . url . '?controller=carta&action=index');
        exit;
    }


}
