
<div class="container py-5">

    <h2 class="mb-4">Personaliza tu producto</h2>

    <div class="card shadow p-4">

        <div class="row">

            <div class="col-md-5">

                <?php if ($producto->getImagen()): ?>
                    <img
                        src="<?= htmlspecialchars($producto->getImagen()) ?>"
                        class="img-fluid rounded"
                        alt="<?= htmlspecialchars($producto->getNombre()) ?>"
                    >
                <?php endif; ?>

                <h3 class="mt-3">
                    <?= htmlspecialchars($producto->getNombre()) ?>
                </h3>

                <p>
                    <?= htmlspecialchars($producto->getDescripcion() ?? '') ?>
                </p>

                <h4>
                    Precio base:
                    <?= number_format($producto->getPrecio(), 2) ?> €
                </h4>

            </div>

            <div class="col-md-7">

                <form method="POST"
                      action="?controller=producto&action=agregarCarrito">

                    <input type="hidden"
                           name="producto_id"
                           value="<?= $producto->getId() ?>">

                    <h4>Ingredientes originales</h4>

                    <?php foreach ($ingredientesProducto as $ingrediente): ?>

                        <div class="form-check mb-2">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="ingredientes[]"
                                value="<?= $ingrediente->id ?>"
                                id="ingrediente<?= $ingrediente->id ?>"
                                checked
                            >

                            <label class="form-check-label"
                                   for="ingrediente<?= $ingrediente->id ?>">

                                <?= htmlspecialchars($ingrediente->nombre) ?>

                                <?php if ((float) $ingrediente->precio_extra > 0): ?>
                                    (+<?= number_format(
                                        $ingrediente->precio_extra,
                                        2
                                    ) ?> €)
                                <?php endif; ?>

                            </label>

                        </div>

                    <?php endforeach; ?>

                    <hr>

                    <h4>Añadir ingredientes</h4>

                    <?php foreach ($todosIngredientes as $ingrediente): ?>

                        <div class="form-check mb-2">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="ingredientes_extra[]"
                                value="<?= $ingrediente->id ?>"
                                id="extra<?= $ingrediente->id ?>"
                            >

                            <label class="form-check-label"
                                   for="extra<?= $ingrediente->id ?>">

                                <?= htmlspecialchars($ingrediente->nombre) ?>

                                (+<?= number_format(
                                    $ingrediente->precio_extra,
                                    2
                                ) ?> €)

                            </label>

                        </div>

                    <?php endforeach; ?>

                    <button type="submit"
                            class="btn btn-warning mt-4 w-100">

                        Añadir al carrito

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>
