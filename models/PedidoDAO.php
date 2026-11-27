
<?php

require_once __DIR__ . '/Pedido.php';
require_once __DIR__ . '/../config/dataBase.php';

class PedidoDAO
{
    public static function getAll(): array
    {
        $con = DataBase::connect();

        $sql = "SELECT * FROM pedidos ORDER BY fecha_pedido DESC";
        $resultado = $con->query($sql);

        $pedidos = [];

        while ($fila = $resultado->fetch_object()) {
            $pedidos[] = self::mapearPedido($fila);
        }

        $con->close();

        return $pedidos;
    }

    public static function getById(int $id): ?Pedido
    {
        $con = DataBase::connect();

        $sql = "SELECT * FROM pedidos WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $fila = $stmt->get_result()->fetch_object();

        $stmt->close();
        $con->close();

        return $fila ? self::mapearPedido($fila) : null;
    }

    public static function getDetalles(int $pedido_id): array
    {
        $con = DataBase::connect();

        $sql = "SELECT *
                FROM pedido_detalle
                WHERE pedido_id = ?
                ORDER BY id";

        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $pedido_id);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $detalles = [];

        while ($fila = $resultado->fetch_assoc()) {
            $detalles[] = $fila;
        }

        $stmt->close();
        $con->close();

        return $detalles;
    }

    public static function getDetallesCompletos(int $pedido_id): array
    {
        $con = DataBase::connect();

        $sql = "SELECT
                    pd.id AS detalle_id,
                    pd.pedido_id,
                    pd.producto_id,
                    pd.nombre_producto,
                    pd.precio_unitario,
                    pd.cantidad,
                    pd.subtotal,
                    pdi.id AS ingrediente_detalle_id,
                    pdi.ingrediente_id,
                    pdi.nombre_ingrediente,
                    pdi.precio_extra,
                    pdi.accion,
                    pdi.cantidad AS cantidad_ingrediente
                FROM pedido_detalle pd
                LEFT JOIN pedido_detalle_ingrediente pdi
                    ON pd.id = pdi.pedido_detalle_id
                WHERE pd.pedido_id = ?
                ORDER BY pd.id, pdi.id";

        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $pedido_id);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $detalles = [];

        while ($fila = $resultado->fetch_assoc()) {
            $detalles[] = $fila;
        }

        $stmt->close();
        $con->close();

        return $detalles;
    }

    /**
     * Crea el pedido completo a partir del carrito de la sesión.
     * Los precios y los ingredientes se validan de nuevo en MySQL.
     */
    public static function crearDesdeCarrito(
        int $usuarioId,
        array $carrito,
        ?array $descuentoSesion = null
    ): int {
        if ($usuarioId <= 0 || empty($carrito)) {
            throw new InvalidArgumentException('El pedido no contiene datos válidos.');
        }

        $con = DataBase::connect();

        try {
            $con->begin_transaction();

            $lineas = [];
            $subtotalPedido = 0.00;

            foreach ($carrito as $item) {
                $productoId = filter_var(
                    $item['producto_id'] ?? null,
                    FILTER_VALIDATE_INT
                );

                $cantidad = filter_var(
                    $item['cantidad'] ?? 1,
                    FILTER_VALIDATE_INT
                );

                if (!$productoId || $productoId <= 0 || !$cantidad || $cantidad < 1 || $cantidad > 20) {
                    throw new InvalidArgumentException('Hay un producto o una cantidad no válida.');
                }

                // Recuperar el producto y su precio actual.
                $sqlProducto = "SELECT
                                    id,
                                    nombre,
                                    precio,
                                    categoria_id,
                                    activo
                                FROM productos
                                WHERE id = ?
                                FOR UPDATE";

                $stmtProducto = $con->prepare($sqlProducto);
                $stmtProducto->bind_param("i", $productoId);
                $stmtProducto->execute();

                $producto = $stmtProducto->get_result()->fetch_assoc();
                $stmtProducto->close();

                if (!$producto || !(bool) $producto['activo']) {
                    throw new RuntimeException('Uno de los productos ya no está disponible.');
                }

                $categoriaId = (int) $producto['categoria_id'];
                $esPlato = in_array($categoriaId, [1, 2, 3], true);

                $precioBase = (float) $producto['precio'];
                $precioExtras = 0.00;
                $ingredientesGuardar = [];

                if ($esPlato) {
                    // Obtener los ingredientes originales activos del plato.
                    $sqlOriginales = "SELECT
                                        i.id,
                                        i.nombre,
                                        pi.cantidad
                                      FROM producto_ingrediente pi
                                      INNER JOIN ingredientes i
                                        ON i.id = pi.ingrediente_id
                                      WHERE pi.producto_id = ?
                                        AND i.activo = 1";

                    $stmtOriginales = $con->prepare($sqlOriginales);
                    $stmtOriginales->bind_param("i", $productoId);
                    $stmtOriginales->execute();

                    $resultadoOriginales = $stmtOriginales->get_result();
                    $originales = [];

                    while ($fila = $resultadoOriginales->fetch_assoc()) {
                        $originales[(int) $fila['id']] = $fila;
                    }

                    $stmtOriginales->close();

                    $seleccionados = $item['ingredientes'] ?? [];

                    if (!is_array($seleccionados)) {
                        throw new InvalidArgumentException('La selección de ingredientes no es válida.');
                    }

                    $seleccionados = array_values(array_unique(array_filter(
                        array_map('intval', $seleccionados),
                        fn($id) => $id > 0
                    )));

                    foreach ($seleccionados as $idIngrediente) {
                        if (!isset($originales[$idIngrediente])) {
                            throw new InvalidArgumentException('Se ha enviado un ingrediente que no pertenece al plato.');
                        }
                    }

                    // Registrar incluidos y quitados.
                    foreach ($originales as $idOriginal => $ingredienteOriginal) {
                        $incluido = in_array($idOriginal, $seleccionados, true);

                        $ingredientesGuardar[] = [
                            'id' => $idOriginal,
                            'nombre' => $ingredienteOriginal['nombre'],
                            'precio' => 0.00,
                            'accion' => $incluido ? 'incluido' : 'quitado',
                            'cantidad' => (float) $ingredienteOriginal['cantidad']
                        ];
                    }

                    // Recuperar y validar extras actuales.
                    $extrasCarrito = $item['extras'] ?? [];

                    if (!is_array($extrasCarrito) || count($extrasCarrito) > 4) {
                        throw new InvalidArgumentException('La selección de extras no es válida.');
                    }

                    $idsExtras = [];

                    foreach ($extrasCarrito as $extra) {
                        $idExtra = filter_var(
                            $extra['id'] ?? null,
                            FILTER_VALIDATE_INT
                        );

                        if (!$idExtra || $idExtra <= 0) {
                            throw new InvalidArgumentException('Hay un ingrediente extra no válido.');
                        }

                        if (isset($originales[$idExtra]) || in_array($idExtra, $idsExtras, true)) {
                            throw new InvalidArgumentException('No puedes repetir un ingrediente del plato.');
                        }

                        $idsExtras[] = $idExtra;
                    }

                    if (!empty($idsExtras)) {
                        $marcadores = implode(',', array_fill(0, count($idsExtras), '?'));
                        $tipos = str_repeat('i', count($idsExtras));

                        $sqlExtras = "SELECT id, nombre, precio_extra
                                      FROM ingredientes
                                      WHERE activo = 1
                                        AND id IN ($marcadores)";

                        $stmtExtras = $con->prepare($sqlExtras);
                        $stmtExtras->bind_param($tipos, ...$idsExtras);
                        $stmtExtras->execute();

                        $resultadoExtras = $stmtExtras->get_result();
                        $extrasEncontrados = [];

                        while ($fila = $resultadoExtras->fetch_assoc()) {
                            $extrasEncontrados[(int) $fila['id']] = $fila;
                        }

                        $stmtExtras->close();

                        if (count($extrasEncontrados) !== count($idsExtras)) {
                            throw new RuntimeException('Uno de los ingredientes extra ya no está disponible.');
                        }

                        foreach ($idsExtras as $idExtra) {
                            $extra = $extrasEncontrados[$idExtra];
                            $precioExtra = (float) $extra['precio_extra'];

                            $precioExtras += $precioExtra;

                            $ingredientesGuardar[] = [
                                'id' => $idExtra,
                                'nombre' => $extra['nombre'],
                                'precio' => $precioExtra,
                                'accion' => 'extra',
                                'cantidad' => 1.00
                            ];
                        }
                    }
                } else {
                    // Bebidas y postres no admiten ingredientes ni extras.
                    if (
                        !empty($item['ingredientes']) ||
                        !empty($item['extras'])
                    ) {
                        throw new InvalidArgumentException('Este producto no admite modificaciones.');
                    }
                }

                $precioUnitario = round($precioBase + $precioExtras, 2);
                $subtotalLinea = round($precioUnitario * $cantidad, 2);

                $subtotalPedido += $subtotalLinea;

                $lineas[] = [
                    'producto_id' => (int) $producto['id'],
                    'nombre' => $producto['nombre'],
                    'precio_unitario' => $precioUnitario,
                    'cantidad' => $cantidad,
                    'subtotal' => $subtotalLinea,
                    'ingredientes' => $ingredientesGuardar
                ];
            }

            $subtotalPedido = round($subtotalPedido, 2);

            // El descuento se calcula de nuevo a partir del código permitido.
            $porcentajeDescuento = 0;

            if ($descuentoSesion !== null) {
                $codigo = strtoupper(trim((string) ($descuentoSesion['codigo'] ?? '')));

                if ($codigo === 'LENAULT10') {
                    $porcentajeDescuento = 10;
                } elseif ($codigo === 'BIENVENIDA15') {
                    $porcentajeDescuento = 15;
                }
            }

            $descuentoImporte = round(
                $subtotalPedido * $porcentajeDescuento / 100,
                2
            );

            $baseImponible = round(
                $subtotalPedido - $descuentoImporte,
                2
            );

            $iva = round($baseImponible * 0.10, 2);

            $total = round($baseImponible + $iva, 2);

            // Insertar encabezado del pedido.
            $sqlPedido = "INSERT INTO pedidos
                            (usuario_id, estado, subtotal, descuento, iva,
                             total, metodo_pago, estado_pago)
                          VALUES (?, 'pendiente', ?, ?, ?, ?, 'simulado', 'pendiente')";

            $stmtPedido = $con->prepare($sqlPedido);
            $stmtPedido->bind_param(
                "idddd",
                $usuarioId,
                $subtotalPedido,
                $descuentoImporte,
                $iva,
                $total
            );

            $stmtPedido->execute();
            $pedidoId = (int) $con->insert_id;
            $stmtPedido->close();

            // Insertar líneas e ingredientes.
            $sqlDetalle = "INSERT INTO pedido_detalle
                            (pedido_id, producto_id, nombre_producto,
                             precio_unitario, cantidad, subtotal)
                           VALUES (?, ?, ?, ?, ?, ?)";

            $stmtDetalle = $con->prepare($sqlDetalle);

            $sqlIngrediente = "INSERT INTO pedido_detalle_ingrediente
                                (pedido_detalle_id, ingrediente_id,
                                 nombre_ingrediente, precio_extra,
                                 accion, cantidad)
                               VALUES (?, ?, ?, ?, ?, ?)";

            $stmtIngrediente = $con->prepare($sqlIngrediente);

            foreach ($lineas as $linea) {
                $productoId = $linea['producto_id'];
                $nombreProducto = $linea['nombre'];
                $precioUnitario = $linea['precio_unitario'];
                $cantidad = $linea['cantidad'];
                $subtotalLinea = $linea['subtotal'];

                $stmtDetalle->bind_param(
                    "iisdid",
                    $pedidoId,
                    $productoId,
                    $nombreProducto,
                    $precioUnitario,
                    $cantidad,
                    $subtotalLinea
                );

                $stmtDetalle->execute();
                $detalleId = (int) $con->insert_id;

                foreach ($linea['ingredientes'] as $ingrediente) {
                    $ingredienteId = $ingrediente['id'];
                    $nombreIngrediente = $ingrediente['nombre'];
                    $precioExtra = $ingrediente['precio'];
                    $accion = $ingrediente['accion'];
                    $cantidadIngrediente = $ingrediente['cantidad'];

                    $stmtIngrediente->bind_param(
                        "iisdsd",
                        $detalleId,
                        $ingredienteId,
                        $nombreIngrediente,
                        $precioExtra,
                        $accion,
                        $cantidadIngrediente
                    );

                    $stmtIngrediente->execute();
                }
            }

            $stmtDetalle->close();
            $stmtIngrediente->close();

            $con->commit();

            return $pedidoId;

        } catch (Throwable $e) {
            $con->rollback();
            throw $e;

        } finally {
            $con->close();
        }
    }

    public static function update(Pedido $pedido): bool
    {
        $con = DataBase::connect();

        $sql = "UPDATE pedidos
                SET estado = ?, estado_pago = ?
                WHERE id = ?";

        $stmt = $con->prepare($sql);

        $id = $pedido->getId();
        $estado = $pedido->getEstado();
        $estadoPago = $pedido->getEstado_pago();

        $stmt->bind_param("ssi", $estado, $estadoPago, $id);

        $resultado = $stmt->execute();

        $stmt->close();
        $con->close();

        return $resultado;
    }

    public static function delete(int $id): bool
    {
        $con = DataBase::connect();

        $sql = "DELETE FROM pedidos WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $id);

        $resultado = $stmt->execute();

        $stmt->close();
        $con->close();

        return $resultado;
    }

    private static function mapearPedido(object $fila): Pedido
    {
        $pedido = new Pedido();

        $pedido->setId((int) $fila->id);
        $pedido->setUsuario_id((int) $fila->usuario_id);
        $pedido->setEstado($fila->estado);
        $pedido->setFecha_pedido($fila->fecha_pedido);
        $pedido->setSubtotal((float) $fila->subtotal);
        $pedido->setDescuento((float) $fila->descuento);
        $pedido->setIva((float) $fila->iva);
        $pedido->setTotal((float) $fila->total);
        $pedido->setMetodo_pago($fila->metodo_pago);
        $pedido->setEstado_pago($fila->estado_pago);

        return $pedido;
    }
}
