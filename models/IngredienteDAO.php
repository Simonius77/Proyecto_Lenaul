

<?php

require_once __DIR__ . '/../config/dataBase.php';

class IngredienteDAO
{
    /**
     * Obtener los ingredientes activos de un producto.
     */
    public static function getByProductoId(int $producto_id): array
    {
        $con = DataBase::connect();

        $sql = "SELECT
                    i.id,
                    i.nombre,
                    i.precio_extra,
                    pi.cantidad
                FROM producto_ingrediente pi
                INNER JOIN ingredientes i
                    ON pi.ingrediente_id = i.id
                WHERE pi.producto_id = ?
                  AND i.activo = 1
                ORDER BY i.nombre";

        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $producto_id);
        $stmt->execute();

        $resultado = $stmt->get_result();

        $ingredientes = [];

        while ($fila = $resultado->fetch_object()) {
            $ingredientes[] = $fila;
        }

        $stmt->close();
        $con->close();

        return $ingredientes;
    }

    /**
     * Obtener todos los ingredientes activos.
     * Se utilizarán para ofrecer ingredientes adicionales.
     */
    public static function getAll(): array
    {
        $con = DataBase::connect();

        $sql = "SELECT id, nombre, precio_extra
                FROM ingredientes
                WHERE activo = 1
                ORDER BY nombre";

        $resultado = $con->query($sql);

        $ingredientes = [];

        while ($fila = $resultado->fetch_object()) {
            $ingredientes[] = $fila;
        }

        $con->close();

        return $ingredientes;
    }
}
