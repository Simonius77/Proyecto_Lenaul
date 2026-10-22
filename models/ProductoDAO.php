<?php

require_once __DIR__ . '/Producto.php';
require_once __DIR__ . '/../config/dataBase.php';

class ProductoDAO
{
    /**
     * Obtener todos los productos.
     */
    public static function getAll(): array
    {
        $con = DataBase::connect();

        $sql = "SELECT * FROM productos ORDER BY id DESC";
        $resultado = $con->query($sql);

        $productos = [];

        while ($fila = $resultado->fetch_assoc()) {
            $productos[] = self::mapearProducto($fila);
        }

        $con->close();

        return $productos;
    }

    /**
     * Obtener un producto por su ID.
     */
    public static function getById(int $id): ?Producto
    {
        $con = DataBase::connect();

        $sql = "SELECT * FROM productos WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();

        $stmt->close();
        $con->close();

        return $fila ? self::mapearProducto($fila) : null;
    }

    /**
     * Insertar un producto.
     * Devuelve el ID creado o false si falla.
     */
    public static function create(Producto $producto): int|false
    {
        $con = DataBase::connect();

        $categoria_id = $producto->getCategoriaId();
        $nombre = $producto->getNombre();
        $descripcion = $producto->getDescripcion();
        $precio = $producto->getPrecio();
        $imagen = $producto->getImagen();
        $activo = (int) $producto->isActivo();

        $sql = "INSERT INTO productos
                (categoria_id, nombre, descripcion, precio, imagen, activo)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $con->prepare($sql);
        $stmt->bind_param(
            "issdsi",
            $categoria_id,
            $nombre,
            $descripcion,
            $precio,
            $imagen,
            $activo
        );

        $correcto = $stmt->execute();
        $id = $correcto ? $con->insert_id : false;

        $stmt->close();
        $con->close();

        return $id;
    }

    /**
     * Actualizar un producto existente.
     */
    public static function update(Producto $producto): bool
    {
        if ($producto->getId() === null) {
            return false;
        }

        $con = DataBase::connect();

        $id = $producto->getId();
        $categoria_id = $producto->getCategoriaId();
        $nombre = $producto->getNombre();
        $descripcion = $producto->getDescripcion();
        $precio = $producto->getPrecio();
        $imagen = $producto->getImagen();
        $activo = (int) $producto->isActivo();

        $sql = "UPDATE productos
                SET categoria_id = ?,
                    nombre = ?,
                    descripcion = ?,
                    precio = ?,
                    imagen = ?,
                    activo = ?
                WHERE id = ?";

        $stmt = $con->prepare($sql);
        $stmt->bind_param(
            "issdsii",
            $categoria_id,
            $nombre,
            $descripcion,
            $precio,
            $imagen,
            $activo,
            $id
        );

        $correcto = $stmt->execute();

        $stmt->close();
        $con->close();

        return $correcto;
    }

    /**
     * Eliminar un producto por su ID.
     */
    public static function delete(int $id): bool
    {
        $con = DataBase::connect();

        $sql = "DELETE FROM productos WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $id);

        $correcto = $stmt->execute();

        $stmt->close();
        $con->close();

        return $correcto;
    }

    /**
     * Convertir una fila de MySQL en un objeto Producto.
     */
    private static function mapearProducto(array $fila): Producto
    {
        return new Producto(
            categoria_id: (int) $fila['categoria_id'],
            nombre: $fila['nombre'],
            descripcion: $fila['descripcion'],
            precio: (float) $fila['precio'],
            imagen: $fila['imagen'],
            activo: (bool) $fila['activo'],
            id: (int) $fila['id']
        );
    }
}