
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$itemsCarrito = $itemsCarrito ?? [];
$subtotalProductos = $subtotalProductos ?? 0;
$totalUnidades = $totalUnidades ?? 0;
$importeDescuento = $importeDescuento ?? 0;
$porcentajeDescuento = $porcentajeDescuento ?? 0;
$descuentoAplicado = $_SESSION['descuento'] ?? null;
$baseImponible = $baseImponible ?? 0;
$porcentajeIVA = $porcentajeIVA ?? 10;
$importeIVA = $importeIVA ?? 0;
$totalFinal = $totalFinal ?? 0;
?>

<div class="container py-5">

    <!-- CABECERA -->
    <div class="text-center mb-5">

        <h1 class="fw-bold font-monospace text-dark mb-2">
            MI CARRITO
        </h1>

        <div
            class="rounded-pill mx-auto"
            style="width:80px;height:4px;background:#ffc107;">
        </div>

        <p class="text-muted mt-3 fs-5">
            Revisa tu pedido antes de continuar.
        </p>

    </div>

    <!-- MENSAJE DE ÉXITO -->
    <?php if (isset($_SESSION['success'])): ?>

        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm">

            <i class="fas fa-check-circle me-2"></i>

            <?= htmlspecialchars(
                $_SESSION['success'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>

            <?php unset($_SESSION['success']); ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    <?php endif; ?>

    <!-- MENSAJE DE ERROR -->
    <?php if (isset($_SESSION['error'])): ?>

        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm">

            <i class="fas fa-exclamation-circle me-2"></i>

            <?= htmlspecialchars(
                $_SESSION['error'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>

            <?php unset($_SESSION['error']); ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    <?php endif; ?>


    <?php if (empty($itemsCarrito)): ?>

        <!-- CARRITO VACÍO -->
        <div class="text-center py-5">

            <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>

            <h3 class="fw-bold text-dark">
                Tu carrito está vacío
            </h3>

            <p class="text-muted">
                Todavía no has añadido ningún producto.
            </p>

            <a
                href="<?= url ?>?controller=carta&action=index"
                class="btn btn-warning fw-bold mt-3">

                <i class="fas fa-utensils me-2"></i>
                Ver nuestra carta

            </a>

        </div>

    <?php else: ?>

        <div class="row g-4">

            <!-- LISTADO DE PRODUCTOS -->
            <div class="col-lg-8">

                <?php foreach ($itemsCarrito as $indice => $item): ?>

                    <div class="card producto-carrito border-0 shadow-sm rounded-4 mb-3">

                        <div class="card-body p-4">

                            <div class="row align-items-center g-3">

                                <!-- INFORMACIÓN -->
                                <div class="col-md-7">

                                    <h5 class="fw-bold text-dark mb-2">

                                        <?= htmlspecialchars(
                                            $item['nombre'] ?? 'Producto',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </h5>

                                    <!-- INGREDIENTES -->
                                    <?php if (!empty($item['ingredientes_nombres'])): ?>

                                        <p class="small text-muted mb-2">

                                            <strong>Ingredientes:</strong>

                                            <?= htmlspecialchars(
                                                implode(', ', $item['ingredientes_nombres']),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </p>

                                    <?php endif; ?>

                                    <!-- EXTRAS -->
                                    <?php if (!empty($item['extras'])): ?>

                                        <div class="small text-muted mb-2">

                                            <strong>Extras:</strong>

                                            <?php foreach ($item['extras'] as $extra): ?>

                                                <?php if (is_array($extra)): ?>

                                                    <div>
                                                        <i class="fas fa-plus-circle text-success me-1"></i>

                                                        <?= htmlspecialchars(
                                                            $extra['nombre'] ?? '',
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        ) ?>

                                                        (+<?= number_format(
                                                            (float) ($extra['precio'] ?? 0),
                                                            2,
                                                            ',',
                                                            '.'
                                                        ) ?> €)
                                                    </div>

                                                <?php endif; ?>

                                            <?php endforeach; ?>

                                        </div>

                                    <?php endif; ?>

                                    <p class="small text-muted mb-0">

                                        Precio por unidad:

                                        <strong>
                                            <?= number_format(
                                                (float) ($item['precio_unitario'] ?? 0),
                                                2,
                                                ',',
                                                '.'
                                            ) ?> €
                                        </strong>

                                    </p>

                                </div>


                                <!-- CANTIDAD -->
                                <div class="col-md-3">

                                    <form
                                        action="<?= url ?>?controller=carrito&action=actualizarCantidad"
                                        method="POST">

                                        <input
                                            type="hidden"
                                            name="indice"
                                            value="<?= (int) $indice ?>">

                                        <label class="form-label small fw-bold">
                                            Cantidad
                                        </label>

                                        <div class="input-group">

                                            <input
                                                type="number"
                                                name="cantidad"
                                                class="form-control"
                                                value="<?= (int) $item['cantidad'] ?>"
                                                min="1"
                                                max="20"
                                                required>

                                            <button
                                                type="submit"
                                                class="btn btn-outline-secondary"
                                                title="Actualizar cantidad">

                                                <i class="fas fa-sync-alt"></i>

                                            </button>

                                        </div>

                                    </form>

                                </div>


                                <!-- SUBTOTAL Y ELIMINAR -->
                                <div class="col-md-2 text-md-end">

                                    <p class="fw-bold fs-5 text-dark mb-2">

                                        <?= number_format(
                                            (float) ($item['subtotal_linea'] ?? 0),
                                            2,
                                            ',',
                                            '.'
                                        ) ?> €

                                    </p>

                                    <form
                                        action="<?= url ?>?controller=carrito&action=eliminar"
                                        method="POST"
                                        onsubmit="return confirm('¿Quieres eliminar este producto del carrito?');">

                                        <input
                                            type="hidden"
                                            name="indice"
                                            value="<?= (int) $indice ?>">

                                        <button
                                            type="submit"
                                            class="btn btn-outline-danger btn-sm">

                                            <i class="fas fa-trash-alt me-1"></i>
                                            Eliminar

                                        </button>

                                    </form>

                                </div>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>


                <!-- BOTONES INFERIORES -->
                <div class="d-flex flex-wrap justify-content-between gap-2 mt-4">

                    <a
                        href="<?= url ?>?controller=carta&action=index"
                        class="btn btn-outline-dark">

                        <i class="fas fa-arrow-left me-2"></i>
                        Seguir comprando

                    </a>

                    <form
                        action="<?= url ?>?controller=carrito&action=vaciar"
                        method="POST"
                        onsubmit="return confirm('¿Quieres vaciar todo el carrito?');">

                        <button
                            type="submit"
                            class="btn btn-outline-danger">

                            <i class="fas fa-trash me-2"></i>
                            Vaciar carrito

                        </button>

                    </form>

                </div>

            </div>


            <!-- RESUMEN DEL PEDIDO -->
            <div class="col-lg-4">

                <div class="card resumen-carrito border-0 shadow-sm rounded-4">

                    <div class="card-body p-4">

                        <h4 class="fw-bold mb-4">
                            <i class="fas fa-receipt me-2 text-warning"></i>
                            Resumen del pedido
                        </h4>


                        <!-- CANTIDAD DE PRODUCTOS -->
                        <div class="d-flex justify-content-between mb-3">

                            <span class="text-muted">
                                Total de productos
                            </span>

                            <strong>
                                <?= (int) $totalUnidades ?>
                            </strong>

                        </div>


                        <!-- SUBTOTAL -->
                        <div class="d-flex justify-content-between mb-3">

                            <span class="text-muted">
                                Subtotal
                            </span>

                            <strong>
                                <?= number_format(
                                    (float) $subtotalProductos,
                                    2,
                                    ',',
                                    '.'
                                ) ?> €
                            </strong>

                        </div>


                        <!-- CÓDIGO DE DESCUENTO -->
                        <div class="border rounded-3 p-3 mb-3">

                            <label class="form-label fw-bold">

                                <i class="fas fa-tag me-2 text-warning"></i>
                                Código de descuento

                            </label>

                            <?php if (!empty($descuentoAplicado)): ?>

                                <div class="alert alert-success py-2 mb-2">

                                    <strong>
                                        <?= htmlspecialchars(
                                            $descuentoAplicado['codigo'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </strong>

                                    <br>

                                    <small>
                                        <?= htmlspecialchars(
                                            $descuentoAplicado['descripcion'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </small>

                                </div>

                                <form
                                    action="<?= url ?>?controller=carrito&action=quitarDescuento"
                                    method="POST">

                                    <button
                                        type="submit"
                                        class="btn btn-outline-danger btn-sm w-100">

                                        <i class="fas fa-times me-1"></i>
                                        Quitar descuento

                                    </button>

                                </form>

                            <?php else: ?>

                                <form
                                    action="<?= url ?>?controller=carrito&action=aplicarDescuento"
                                    method="POST">

                                    <div class="input-group">

                                        <input
                                            type="text"
                                            name="codigo_descuento"
                                            class="form-control"
                                            placeholder="Introduce tu código"
                                            maxlength="30"
                                            required>

                                        <button
                                            type="submit"
                                            class="btn btn-dark">

                                            Aplicar

                                        </button>

                                    </div>

                                </form>

                                <small class="text-muted d-block mt-2">
                                    Introduce tu código promocional.
                                </small>

                            <?php endif; ?>

                        </div>


                        <!-- DESCUENTO -->
                        <?php if ($importeDescuento > 0): ?>

                            <div class="d-flex justify-content-between mb-3 text-success">

                                <span>
                                    Descuento (<?= number_format(
                                        (float) $porcentajeDescuento,
                                        0
                                    ) ?>%)
                                </span>

                                <strong>
                                    -<?= number_format(
                                        (float) $importeDescuento,
                                        2,
                                        ',',
                                        '.'
                                    ) ?> €
                                </strong>

                            </div>

                        <?php endif; ?>


                        <hr>


                        <!-- BASE IMPONIBLE -->
                        <div class="d-flex justify-content-between mb-3">

                            <span class="text-muted">
                                Base imponible
                            </span>

                            <strong>
                                <?= number_format(
                                    (float) $baseImponible,
                                    2,
                                    ',',
                                    '.'
                                ) ?> €
                            </strong>

                        </div>


                        <!-- IVA -->
                        <div class="d-flex justify-content-between mb-3">

                            <span class="text-muted">
                                IVA (<?= number_format(
                                    (float) $porcentajeIVA,
                                    0
                                ) ?>%)
                            </span>

                            <strong>
                                <?= number_format(
                                    (float) $importeIVA,
                                    2,
                                    ',',
                                    '.'
                                ) ?> €
                            </strong>

                        </div>


                        <hr>


                        <!-- TOTAL FINAL -->
                        <div class="d-flex justify-content-between align-items-center">

                            <span class="fw-bold fs-5">
                                TOTAL
                            </span>

                            <span class="fs-3 fw-bold text-success">

                                <?= number_format(
                                    (float) $totalFinal,
                                    2,
                                    ',',
                                    '.'
                                ) ?> €

                            </span>

                        </div>


                        <!-- FINALIZAR PEDIDO -->
                        <button
                            type="button"
                            class="btn btn-warning fw-bold w-100 mt-4"
                            disabled>

                            <i class="fas fa-check-circle me-2"></i>
                            Finalizar pedido

                        </button>

                        <p class="text-muted small text-center mt-2 mb-0">
                            Próximamente podrás confirmar tu pedido.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    <?php endif; ?>

</div>


<style>

    .producto-carrito,
    .resumen-carrito {
        transition: box-shadow .3s ease;
    }

    .producto-carrito:hover,
    .resumen-carrito:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,.08) !important;
    }

    .btn-warning {
        background-color: #ffc107;
        border: none;
    }

    .btn-warning:hover {
        background-color: #e5ac00;
    }

    .producto-carrito {
        background-color: #fff;
    }

    .resumen-carrito {
        position: sticky;
        top: 20px;
    }

</style>
