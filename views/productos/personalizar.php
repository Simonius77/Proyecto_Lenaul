
<?php

// Comprobar que existe el producto.
if (!isset($producto) || !($producto instanceof Producto)) {
    header('Location: ?controller=carta&action=index');
    exit;
}

$ingredientesProducto = $ingredientesProducto ?? [];
$todosIngredientes = $todosIngredientes ?? [];

$esPlato = $producto instanceof Plato;

// Obtener imagen del producto.
$imagen = $producto->getImagen();

if (!empty($imagen)) {
    if (filter_var($imagen, FILTER_VALIDATE_URL)) {
        $rutaImagen = $imagen;
    } else {
        $rutaImagen = url . ltrim($imagen, '/');
    }
} else {
    $rutaImagen = url . 'Imagenes/default.png';
}

// Obtener IDs de ingredientes originales.
$idsOriginales = array_map(
    fn($ingrediente) => (int) $ingrediente->id,
    $ingredientesProducto
);

// Excluir los ingredientes que ya tiene el plato.
$ingredientesDisponibles = array_filter(
    $todosIngredientes,
    fn($ingrediente) => !in_array(
        (int) $ingrediente->id,
        $idsOriginales,
        true
    )
);

// Mostrar como máximo 4 ingredientes adicionales.
$ingredientesDisponibles = array_slice(
    array_values($ingredientesDisponibles),
    0,
    4
);

?>

<div class="container py-5">

    <h2 class="text-center mb-4">
        <?= $esPlato ? 'Personaliza tu plato' : 'Detalles del producto' ?>
    </h2>

    <!-- Información del producto -->
    <div class="card shadow-sm mb-4">

        <div class="row g-0 align-items-center">

            <div class="col-md-5">

                <img
                    src="<?= htmlspecialchars($rutaImagen, ENT_QUOTES, 'UTF-8') ?>"
                    alt="<?= htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8') ?>"
                    class="img-fluid rounded-start w-100"
                    style="height: 300px; object-fit: cover;"
                    onerror="this.onerror=null; this.src='<?= htmlspecialchars(url . 'Imagenes/default.png', ENT_QUOTES, 'UTF-8') ?>';"
                >

            </div>

            <div class="col-md-7">

                <div class="card-body p-4">

                    <h3>
                        <?= htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8') ?>
                    </h3>

                    <?php if ($producto->getDescripcion()): ?>

                        <p>
                            <?= htmlspecialchars($producto->getDescripcion(), ENT_QUOTES, 'UTF-8') ?>
                        </p>

                    <?php endif; ?>

                    <h4 class="text-success">
                        <?= number_format(
                            (float) $producto->getPrecio(),
                            2,
                            ',',
                            '.'
                        ) ?> €
                    </h4>

                </div>

            </div>

        </div>

    </div>

    <?php if (!$esPlato): ?>

        <!-- BEBIDAS Y POSTRES -->

        <div class="alert alert-warning text-center" role="alert">

            <h5 class="mb-0">
                Este producto no permite modificaciones.
            </h5>

        </div>

        <!-- Añadir bebida o postre directamente al carrito -->

        <form
            action="<?= url ?>?controller=producto&action=agregarCarrito"
            method="POST"
        >

            <input
                type="hidden"
                name="producto_id"
                value="<?= (int) $producto->getId() ?>"
            >

            <div class="d-flex justify-content-center gap-2 flex-wrap">

                <a
                    href="<?= url ?>?controller=carta&action=index"
                    class="btn btn-secondary"
                >
                    Volver a la carta
                </a>

                <button
                    type="submit"
                    class="btn btn-success"
                >
                    Añadir al carrito
                </button>

            </div>

        </form>

    <?php else: ?>

        <!-- PERSONALIZACIÓN DE PLATOS -->

        <form
            action="<?= url ?>?controller=producto&action=agregarCarrito"
            method="POST"
        >

            <input
                type="hidden"
                name="producto_id"
                value="<?= (int) $producto->getId() ?>"
            >

            <!-- Ingredientes originales -->

            <div class="card shadow-sm mb-4">

                <div class="card-body">

                    <h4 class="mb-3">
                        Ingredientes del plato
                    </h4>

                    <?php if (empty($ingredientesProducto)): ?>

                        <p>
                            Este plato no tiene ingredientes registrados.
                        </p>

                    <?php else: ?>

                        <p class="text-muted">
                            Puedes quitar los ingredientes que no quieras.
                        </p>

                        <?php foreach ($ingredientesProducto as $ingrediente): ?>

                            <div class="form-check mb-2">

                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="ingredientes[]"
                                    value="<?= (int) $ingrediente->id ?>"
                                    id="ingrediente<?= (int) $ingrediente->id ?>"
                                    checked
                                >

                                <label
                                    class="form-check-label"
                                    for="ingrediente<?= (int) $ingrediente->id ?>"
                                >
                                    <?= htmlspecialchars(
                                        $ingrediente->nombre,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </label>

                            </div>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </div>

            </div>

            <!-- Ingredientes adicionales -->

            <div class="card shadow-sm mb-4">

                <div class="card-body">

                    <h4 class="mb-3">
                        Ingredientes adicionales
                    </h4>

                    <p class="text-muted">
                        Puedes añadir hasta 4 ingredientes adicionales.
                    </p>

                    <?php if (empty($ingredientesDisponibles)): ?>

                        <p>
                            No hay ingredientes adicionales disponibles.
                        </p>

                    <?php else: ?>

                        <?php foreach ($ingredientesDisponibles as $ingrediente): ?>

                            <div class="form-check mb-2">

                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="extras[]"
                                    value="<?= (int) $ingrediente->id ?>"
                                    id="extra<?= (int) $ingrediente->id ?>"
                                >

                                <label
                                    class="form-check-label"
                                    for="extra<?= (int) $ingrediente->id ?>"
                                >

                                    <?= htmlspecialchars(
                                        $ingrediente->nombre,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                    (+<?= number_format(
                                        (float) $ingrediente->precio_extra,
                                        2,
                                        ',',
                                        '.'
                                    ) ?> €)

                                </label>

                            </div>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </div>

            </div>

            <!-- Botones -->

            <div class="d-flex justify-content-between flex-wrap gap-2">

                <a
                    href="<?= url ?>?controller=carta&action=index"
                    class="btn btn-secondary"
                >
                    Volver a la carta
                </a>

                <button
                    type="submit"
                    class="btn btn-success"
                >
                    Añadir al carrito
                </button>

            </div>

        </form>

    <?php endif; ?>

</div>
