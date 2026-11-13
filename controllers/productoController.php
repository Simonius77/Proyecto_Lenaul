
<?php

require_once __DIR__ . '/../models/ProductoDAO.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Plato.php';
require_once __DIR__ . '/../models/Bebida.php';
require_once __DIR__ . '/../models/Postre.php';
require_once __DIR__ . '/../models/CategoriaDAO.php';
require_once __DIR__ . '/../models/IngredienteDAO.php';

class productoController {

    public function index() {

        $productos = ProductoDAO::getAll();

        $view = "views/productos/listado.php";
        include 'views/main.php';
    }

    public function create() {

        $categorias = CategoriaDAO::getAll();

        $view = "views/productos/create.php";
        include 'views/main.php';
    }

    public function store() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=producto');
            exit;
        }

        $categoria_id = $_POST['categoria_id'] ?? '';
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precio = $_POST['precio'] ?? '';
        $activo = isset($_POST['activo']);

        if (
            !ctype_digit((string) $categoria_id) ||
            (int) $categoria_id < 1 ||
            $nombre === '' ||
            !is_numeric($precio) ||
            !is_finite((float) $precio) ||
            (float) $precio < 0
        ) {
            header('Location: ?controller=producto&action=create');
            exit;
        }

        $categoria = CategoriaDAO::getById((int) $categoria_id);

        if ($categoria === null || !$categoria->getActivo()) {
            header('Location: ?controller=producto&action=create');
            exit;
        }

        // Gestionar la imagen.
        $imagen = null;

        if (
            isset($_FILES['imagen']) &&
            $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE
        ) {

            if ($_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
                header('Location: ?controller=producto&action=create');
                exit;
            }

            if ($_FILES['imagen']['size'] > 3 * 1024 * 1024) {
                header('Location: ?controller=producto&action=create');
                exit;
            }

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($_FILES['imagen']['tmp_name']);

            $tiposPermitidos = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp'
            ];

            if (!isset($tiposPermitidos[$mime])) {
                header('Location: ?controller=producto&action=create');
                exit;
            }

            $carpeta = dirname(__DIR__) . '/Imagenes';

            if (
                !is_dir($carpeta) &&
                !mkdir($carpeta, 0755, true) &&
                !is_dir($carpeta)
            ) {
                header('Location: ?controller=producto&action=create');
                exit;
            }

            $nombreArchivo = bin2hex(random_bytes(16))
                . '.' . $tiposPermitidos[$mime];

            $destino = $carpeta . '/' . $nombreArchivo;

            if (!move_uploaded_file(
                $_FILES['imagen']['tmp_name'],
                $destino
            )) {
                header('Location: ?controller=producto&action=create');
                exit;
            }

            $imagen = 'Imagenes/' . $nombreArchivo;
        }

        // Crear la subclase según la categoría.
        $nombreCategoria = mb_strtolower(trim($categoria->getNombre()));

        $datosProducto = [
            'categoria_id' => (int) $categoria_id,
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio' => (float) $precio,
            'imagen' => $imagen,
            'activo' => $activo
        ];

        if (str_contains($nombreCategoria, 'bebida')) {
            $producto = new Bebida(...$datosProducto);
        } elseif (str_contains($nombreCategoria, 'postre')) {
            $producto = new Postre(...$datosProducto);
        } else {
            $producto = new Plato(...$datosProducto);
        }

        ProductoDAO::create($producto);

        header('Location: ?controller=producto');
        exit;
    }

    public function destroy() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=producto');
            exit;
        }

        if (
            !isset($_GET['id']) ||
            !ctype_digit((string) $_GET['id']) ||
            (int) $_GET['id'] < 1
        ) {
            header('Location: ?controller=producto');
            exit;
        }

        ProductoDAO::delete((int) $_GET['id']);

        header('Location: ?controller=producto');
        exit;
    }

    public function show() {

        if (
            !isset($_GET['id']) ||
            !ctype_digit((string) $_GET['id']) ||
            (int) $_GET['id'] < 1
        ) {
            header('Location: ?controller=producto');
            exit;
        }

        $producto = ProductoDAO::getById((int) $_GET['id']);

        if ($producto === null) {
            header('Location: ?controller=producto');
            exit;
        }

        $view = "views/productos/show.php";
        include 'views/main.php';
    }

    public function personalizar() {

        if (
            !isset($_GET['id']) ||
            !ctype_digit((string) $_GET['id']) ||
            (int) $_GET['id'] < 1
        ) {
            header('Location: ?controller=carta&action=index');
            exit;
        }

        $id = (int) $_GET['id'];

        // Obtener el producto.
        $producto = ProductoDAO::getById($id);

        if ($producto === null || !$producto->getActivo()) {
            header('Location: ?controller=carta&action=index');
            exit;
        }

        // Obtener ingredientes actuales del producto.
        $ingredientesProducto = IngredienteDAO::getByProductoId($id);

        // Obtener todos los ingredientes disponibles.
        $todosIngredientes = IngredienteDAO::getAll();

        // Cargar la vista de personalización.
        $view = "views/productos/personalizar.php";
        include 'views/main.php';
    }

    public function agregarCarrito() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=carta&action=index');
            exit;
        }

        $productoId = filter_input(
            INPUT_POST,
            'producto_id',
            FILTER_VALIDATE_INT
        );

        if (!$productoId || $productoId < 1) {
            header('Location: ?controller=carta&action=index');
            exit;
        }

        $producto = ProductoDAO::getById($productoId);

        if ($producto === null || !$producto->getActivo()) {
            header('Location: ?controller=carta&action=index');
            exit;
        }

        // Ingredientes originales.
        $originales = IngredienteDAO::getByProductoId($productoId);

        $seleccionados = $_POST['ingredientes'] ?? [];
        $extras = $_POST['ingredientes_extra'] ?? [];

        if (!is_array($seleccionados) || !is_array($extras)) {
            header('Location: ?controller=producto&action=personalizar&id=' . $productoId);
            exit;
        }

        $idsOriginales = array_map(
            fn($i) => (int) $i->id,
            $originales
        );

        // Filtrar ingredientes originales seleccionados.
        $idsSeleccionados = array_values(array_unique(array_filter(
            array_map('intval', $seleccionados),
            fn($id) => in_array($id, $idsOriginales, true)
        )));

        // Los extras no pueden duplicar ingredientes originales.
        $idsExtras = array_values(array_unique(array_filter(
            array_map('intval', $extras),
            fn($id) => $id > 0 && !in_array($id, $idsOriginales, true)
        )));

        // Comprobar que los extras existen y están activos.
        $ingredientesExtra = IngredienteDAO::getByIds($idsExtras);

        if (count($ingredientesExtra) !== count($idsExtras)) {
            header('Location: ?controller=producto&action=personalizar&id=' . $productoId);
            exit;
        }

        // Calcular el precio.
        $precio = $producto->getPrecio();

        foreach ($ingredientesExtra as $ingrediente) {
            $precio += (float) $ingrediente->precio_extra;
        }

        // Preparar ingredientes del carrito.
        $ingredientesCarrito = [];

        foreach ($originales as $ingrediente) {

            if (in_array((int) $ingrediente->id, $idsSeleccionados, true)) {

                $ingredientesCarrito[] = [
                    'id' => (int) $ingrediente->id,
                    'nombre' => $ingrediente->nombre,
                    'extra' => false
                ];
            }
        }

        foreach ($ingredientesExtra as $ingrediente) {

            $ingredientesCarrito[] = [
                'id' => (int) $ingrediente->id,
                'nombre' => $ingrediente->nombre,
                'extra' => true
            ];
        }

        // Iniciar sesión.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        // Añadir producto al carrito.
        $_SESSION['carrito'][] = [
            'producto_id' => $producto->getId(),
            'nombre' => $producto->getNombre(),
            'precio' => $precio,
            'cantidad' => 1,
            'ingredientes' => $ingredientesCarrito
        ];

        header('Location: ?controller=producto&action=carrito');
        exit;
    }
}
