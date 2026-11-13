

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

    /**
     * Obtener ingredientes activos por sus IDs.
     */
    public static function getByIds(array $ids): array {
        if (empty($ids)) {
            return [];
        }

        $con = DataBase::connect();

        $ids = array_values(array_unique(array_filter(
            array_map('intval', $ids),
            fn($id) => $id > 0
        )));

        if (empty($ids)) {
            $con->close();
            return [];
        }

        $marcadores = implode(',', array_fill(0, count($ids), '?'));
        $tipos = str_repeat('i', count($ids));

        $sql = "SELECT id, nombre, precio_extra
                FROM ingredientes
                WHERE activo = 1
                AND id IN ($marcadores)";

        $stmt = $con->prepare($sql);
        $stmt->bind_param($tipos, ...$ids);
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
    
}
