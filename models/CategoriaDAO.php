<?php

require_once __DIR__ . '/Categoria.php';
require_once __DIR__ . '/../config/dataBase.php';

class CategoriaDAO
{
    /**
     * Obtener todas las categorías activas.
     */
    public static function getAll(): array
    {
        $con = DataBase::connect();

        $sql = "SELECT id, nombre, descripcion, activa
                FROM categorias
                WHERE activa = 1
                ORDER BY id ASC";

        $resultado = $con->query($sql);

        $categorias = [];

        while ($fila = $resultado->fetch_assoc()) {
            $categorias[] = self::mapearCategoria($fila);
        }

        $con->close();

        return $categorias;
    }

    /**
     * Obtener una categoría por su ID.
     */
    public static function getById(int $id): ?Categoria
    {
        $con = DataBase::connect();

        $sql = "SELECT id, nombre, descripcion, activa
                FROM categorias
                WHERE id = ?";

        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();

        $stmt->close();
        $con->close();

        if (!$fila) {
            return null;
        }

        return self::mapearCategoria($fila);
    }

    /**
     * Crear una categoría.
     */
    public static function create(Categoria $categoria): int|false
    {
        $con = DataBase::connect();

        $nombre = $categoria->getNombre();
        $descripcion = $categoria->getDescripcion();
        $activa = (int) $categoria->getActivo();

        $sql = "INSERT INTO categorias
                (nombre, descripcion, activa)
                VALUES (?, ?, ?)";

        $stmt = $con->prepare($sql);

        $stmt->bind_param(
            "ssi",
            $nombre,
            $descripcion,
            $activa
        );

        $correcto = $stmt->execute();

        $id = $correcto ? $con->insert_id : false;

        $stmt->close();
        $con->close();

        return $id;
    }

    /**
     * Actualizar una categoría.
     */
    public static function update(Categoria $categoria): bool
    {
        if ($categoria->getId() === null) {
            return false;
        }

        $con = DataBase::connect();

        $id = $categoria->getId();
        $nombre = $categoria->getNombre();
        $descripcion = $categoria->getDescripcion();
        $activa = (int) $categoria->getActivo();

        $sql = "UPDATE categorias
                SET nombre = ?,
                    descripcion = ?,
                    activa = ?
                WHERE id = ?";

        $stmt = $con->prepare($sql);

        $stmt->bind_param(
            "ssii",
            $nombre,
            $descripcion,
            $activa,
            $id
        );

        $correcto = $stmt->execute();

        $stmt->close();
        $con->close();

        return $correcto;
    }

    /**
     * Eliminar una categoría.
     */
    public static function delete(int $id): bool
    {
        $con = DataBase::connect();

        $sql = "DELETE FROM categorias WHERE id = ?";

        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $id);

        $correcto = $stmt->execute();

        $stmt->close();
        $con->close();

        return $correcto;
    }

    /**
     * Convertir una fila de MySQL en un objeto Categoria.
     */
    private static function mapearCategoria(array $fila): Categoria
    {
        return new Categoria(
            nombre: $fila['nombre'],
            descripcion: $fila['descripcion'],
            activo: (bool) $fila['activa'],
            id: (int) $fila['id']
        );
    }
}
