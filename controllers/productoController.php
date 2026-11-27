
<?php
//llamamos a los modelos que vamos a usar
require_once __DIR__ . '/../models/ProductoDAO.php';
require_once __DIR__ . '/../models/IngredienteDAO.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Plato.php';
require_once __DIR__ . '/../models/Bebida.php';
require_once __DIR__ . '/../models/Postre.php';

class productoController {


private function esAdministrador(): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    return isset($_SESSION['usuario_id'])
        && isset($_SESSION['usuario_rol'])
        && strtolower(trim((string) $_SESSION['usuario_rol'])) === 'admin';
}


//API
//Ejemplo de api clase magistral 18/11/26 David Navarro
//Funcion API
    
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


        // CREAR UN PRODUCTO
        if ($metodo === "POST" && $funcion === "crear") {

            if (!$this->esAdministrador()) {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'mensaje' => 'No tienes permisos para crear productos'
                ]);
                exit;
            }

            $datos = json_decode(file_get_contents("php://input"), true);

            if (!is_array($datos)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'mensaje' => 'Datos JSON no válidos'
                ]);
                exit;
            }

            $producto = $this->crearObjetoProducto($datos);

            $id = ProductoDAO::create($producto);

            if ($id === false) {
                throw new RuntimeException('No se pudo crear el producto');
            }

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'mensaje' => 'Producto creado correctamente',
                'id' => $id
            ]);
            exit;
        }


        // ACTUALIZAR UN PRODUCTO
        if ($metodo === "PUT" && $funcion === "actualizar") {

            if (!$this->esAdministrador()) {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'mensaje' => 'No tienes permisos para modificar productos'
                ]);
                exit;
            }

            $datos = json_decode(file_get_contents("php://input"), true);

            if (
                !is_array($datos) ||
                !isset($datos['id']) ||
                !filter_var($datos['id'], FILTER_VALIDATE_INT) ||
                (int) $datos['id'] <= 0
            ) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'mensaje' => 'ID de producto no válido'
                ]);
                exit;
            }

            $id = (int) $datos['id'];

            $existente = ProductoDAO::getById($id);

            if (!$existente) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'mensaje' => 'El producto no existe'
                ]);
                exit;
            }

            $producto = $this->crearObjetoProducto($datos, $id);

            $actualizado = ProductoDAO::update($producto);

            echo json_encode([
                'success' => $actualizado,
                'mensaje' => $actualizado
                    ? 'Producto actualizado correctamente'
                    : 'No se pudo actualizar el producto'
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

            // Comprueba aqui que el usuario tiene permisos de administrador.
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

//Crear un objeto Producto a partir de los datos recibidos, validando los campos y determinando la clase correcta según la categoría.
private function crearObjetoProducto(array $datos, ?int $id = null): Producto
{
    $categoriaId = filter_var(
        $datos['categoria_id'] ?? null,
        FILTER_VALIDATE_INT
    );

    $nombre = trim($datos['nombre'] ?? '');
    $descripcion = trim($datos['descripcion'] ?? '');
    $precio = $datos['precio'] ?? null;
    $imagen = trim($datos['imagen'] ?? '');
    $activo = $datos['activo'] ?? true;

    if (!$categoriaId || $categoriaId <= 0) {
        throw new InvalidArgumentException('Categoría no válida');
    }

    if ($nombre === '' || strlen($nombre) > 255) {
        throw new InvalidArgumentException('Nombre no válido');
    }

    if (!is_numeric($precio) || !is_finite((float) $precio) || (float) $precio < 0) {
        throw new InvalidArgumentException('Precio no válido');
    }

    if (!is_bool($activo) && !in_array($activo, [0, 1, '0', '1'], true)) {
        throw new InvalidArgumentException('Estado no válido');
    }

    $con = DataBase::connect();

    $sql = "SELECT nombre FROM categorias WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $categoriaId);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $categoria = $resultado->fetch_assoc();

    $stmt->close();
    $con->close();

    if (!$categoria) {
        throw new InvalidArgumentException('La categoría no existe');
    }

    $nombreCategoria = mb_strtolower(trim($categoria['nombre']));

    if (str_contains($nombreCategoria, 'bebida')) {
        $clase = Bebida::class;
    } elseif (str_contains($nombreCategoria, 'postre')) {
        $clase = Postre::class;
    } elseif (in_array($nombreCategoria, ['entrantes', 'primeros', 'segundos'], true)) {
        $clase = Plato::class;
    } else {
        throw new InvalidArgumentException('Tipo de categoría no reconocido');
    }

    return new $clase(
        (int) $categoriaId,
        $nombre,
        $descripcion !== '' ? $descripcion : null,
        (float) $precio,
        $imagen !== '' ? $imagen : null,
        (bool) $activo,
        $id
    );
}


    
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
