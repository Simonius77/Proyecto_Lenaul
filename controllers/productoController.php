
<?php

include_once("models/ProductoDAO.php");
include_once("models/Producto.php");

class productoController
{
    public function index()
    {
        $productos = ProductoDAO::getAll();
        $view = "views/productos/listado.php";
        include_once 'views/main.php';
    }

    public function create()
    {
        $view = "views/productos/create.php";
        include_once 'views/main.php';
    }

    public function store()
    {
        $categoria_id = $_POST['categoria_id'] ?? '';
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precio = $_POST['precio'] ?? '';
        $imagen = trim($_POST['imagen'] ?? '');
        $activo = isset($_POST['activo']) ? 1 : 0;

        if (
            !ctype_digit((string) $categoria_id) ||
            (int) $categoria_id < 1 ||
            $nombre === '' ||
            !is_numeric($precio) ||
            (float) $precio < 0
        ) {
            header('Location:?controller=producto&action=create');
            exit;
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

        header('Location:?controller=producto');
        exit;
    }

    public function destroy()
    {
        if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
            header('Location:?controller=producto');
            exit;
        }

        ProductoDAO::delete((int) $_GET['id']);

        header('Location:?controller=producto');
        exit;
    }

    public function show()
    {
        if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
            header('Location:?controller=producto');
            exit;
        }

        $producto = ProductoDAO::getById((int) $_GET['id']);

        if ($producto === null) {
            header('Location:?controller=producto');
            exit;
        }

        $view = "views/productos/show.php";
        include_once 'views/main.php';
    }
}
