


<?php

require_once __DIR__ . '/../models/ProductoDAO.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/CategoriaDAO.php';

class productoController
{
    public function index()
    {
        $productos = ProductoDAO::getAll();

        $view = "views/productos/listado.php";
        include 'views/main.php';
    }

    public function create()
    {
        // Cargar las categorías activas para el desplegable.
        $categorias = CategoriaDAO::getAll();

        $view = "views/productos/create.php";
        include 'views/main.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=producto');
            exit;
        }

        $categoria_id = $_POST['categoria_id'] ?? '';
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precio = $_POST['precio'] ?? '';
        $activo = isset($_POST['activo']) ? 1 : 0;

        // Validar los datos básicos.
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

        // Comprobar que la categoría existe y está activa.
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

            // Limitar el tamaño a 3 MB.
            if ($_FILES['imagen']['size'] > 3 * 1024 * 1024) {
                header('Location: ?controller=producto&action=create');
                exit;
            }

            // Validar el tipo real del archivo.
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

        $producto = new Producto(
            categoria_id: (int) $categoria_id,
            nombre: $nombre,
            descripcion: $descripcion,
            precio: (float) $precio,
            imagen: $imagen,
            activo: $activo
        );

        ProductoDAO::create($producto);

        header('Location: ?controller=producto');
        exit;
    }

    public function destroy()
    {
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

    public function show()
    {
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
}
