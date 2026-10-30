<!-- Página home -->

<!-- Banner Principal -->
<div class="container-fluid p-0 mb-5">
    <img
        src="<?= htmlspecialchars(url . 'Imagenes/banner_home.webp', ENT_QUOTES, 'UTF-8') ?>"
        alt="Banner Lenault"
        class="img-fluid w-100"
        style="height: 400px; object-fit: cover;"
    >
</div>

<section class="container">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2 class="titulo-seccion">Nuestros Platos</h2>
            <hr class="w-25 mx-auto">
        </div>
    </div>

    <!-- Grid de Productos -->
    <div class="row g-0">
        <?php if (!empty($listaproductos)): ?>
            <?php foreach ($listaproductos as $producto): ?>
                <div class="col-12 col-md-3 d-flex justify-content-center mb-4">
                    <div class="contenido-producto mb-5" style="width:24rem;">

                        <?php if (!empty($producto->getImagen())): ?>
                            <img
                                src="<?= htmlspecialchars(url . ltrim($producto->getImagen(), '/'), ENT_QUOTES, 'UTF-8') ?>"
                                class="card-img"
                                alt="<?= htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8') ?>"
                                style="width: 100%; height: 220px; object-fit: cover;"
                            >
                        <?php else: ?>
                            <div class="text-center p-5">
                                Imagen no disponible
                            </div>
                        <?php endif; ?>

                        <div class="linea-amarilla"></div>

                        <div class="cuerpo-productos">
                            <h4 class="titulo-producto">
                                <?= htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8') ?>
                            </h4>

                            <p class="descripcion-producto">
                                <?= htmlspecialchars($producto->getDescripcion() ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </p>

                            <p>
                                <strong><?= htmlspecialchars((string) $producto->getPrecio(), ENT_QUOTES, 'UTF-8') ?> €</strong>
                            </p>

                            <a
                                class="boton-agregar texto-boton"
                                href="<?= htmlspecialchars(url . '?controller=producto&action=index', ENT_QUOTES, 'UTF-8') ?>"
                            >
                                Ver en Carta
                            </a>
                        </div>

                        <div class="linea-amarilla"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">No hay productos disponibles por el momento.</p>
        <?php endif; ?>
    </div>
</section>