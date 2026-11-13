<!--esta linea solo corrige un fallo del editor que no reconoce la variable $producto 
    como un objeto Producto, me lo ha pasado la IA por que el fallo me volvia loco-->
<?php
/** @var Producto $producto */
?>

<?php
$ingredientesProducto = $ingredientesProducto ?? [];
$todosIngredientes = $todosIngredientes ?? [];

$esPlato = $producto instanceof Plato;

// Cargar la imagen del producto
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

// Obtener los IDs de los ingredientes originales
$idsOriginales = array_map(
    fn($ingrediente) => (int) $ingrediente->id,
    $ingredientesProducto
);

// Excluir los ingredientes que ya tiene el producto
$ingredientesDisponibles = array_filter(
    $todosIngredientes,
    fn($ingrediente) => !in_array(
        (int) $ingrediente->id,
        $idsOriginales,
        true
    )
);

// Mostrar como máximo 4 ingredientes adicionales
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
                    src="<?= htmlspecialchars($rutaImagen) ?>"
                    alt="<?= htmlspecialchars($producto->getNombre()) ?>"
                    class="img-fluid rounded-start w-100"
                    style="height: 300px; object-fit: cover;"
                    onerror="this.onerror=null; this.src='<?= htmlspecialchars(url . 'Imagenes/default.png') ?>';"
                >
            </div>

            <div class="col-md-7">
                <div class="card-body p-4">

                    <h3>
                        <?= htmlspecialchars($producto->getNombre()) ?>
                    </h3>

                    <?php if ($producto->getDescripcion()): ?>
                        <p>
                            <?= htmlspecialchars($producto->getDescripcion()) ?>
                        </p>
                    <?php endif; ?>

                    <h4 class="text-success">
                        <?= number_format($producto->getPrecio(), 2, ',', '.') ?> €
                    </h4>

                </div>
            </div>

        </div>
    </div>

    <?php if (!$esPlato): ?>

        <!-- Mensaje para bebidas y postres -->
        <div class="alert alert-warning text-center" role="alert">
            <h5 class="mb-0">
                Este producto no permite modificaciones.
            </h5>
        </div>

        <div class="text-center">
            <a href="<?= url ?>?controller=carta&action=index"
               class="btn btn-primary">
                Volver a la carta
            </a>
        </div>

    <?php else: ?>

        <!-- Personalización disponible solamente para platos -->
        <form
            action="<?= url ?>?controller=producto&action=agregarCarrito"
            method="POST"
        >

            <input
                type="hidden"
                name="producto_id"
                value="<?= (int) $producto->getId() ?>"
            >

            <div class="card shadow-sm mb-4">
                <div class="card-body">

                    <h4 class="mb-3">Ingredientes del plato</h4>

                    <?php if (empty($ingredientesProducto)): ?>

                        <p>Este plato no tiene ingredientes registrados.</p>

                    <?php else: ?>

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
                                    <?= htmlspecialchars($ingrediente->nombre) ?>
                                </label>

                            </div>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">

                    <h4 class="mb-3">Ingredientes adicionales</h4>

                    <p class="text-muted">
                        Puedes añadir hasta 4 ingredientes adicionales.
                    </p>

                    <?php if (empty($ingredientesDisponibles)): ?>

                        <p>No hay ingredientes adicionales disponibles.</p>

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
                                    <?= htmlspecialchars($ingrediente->nombre) ?>

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

            <div class="d-flex justify-content-between flex-wrap gap-2">

                <a
                    href="<?= url ?>?controller=carta&action=index"
                    class="btn btn-secondary"
                >
                    Volver a la carta
                </a>

                <button type="submit" class="btn btn-success">
                    Añadir al carrito
                </button>

            </div>

        </form>

    <?php endif; ?>

</div>
