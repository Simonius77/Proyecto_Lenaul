
<?php
// Comprobamos que el controlador haya enviado los productos.
if (!isset($listaproductos) || !is_array($listaproductos)) {
    $listaproductos = [];
}
?>

<div class="container py-5">

    <!-- CABECERA -->
    <div class="text-center mb-5">
        <h1 class="fw-bold font-monospace text-dark mb-2">
            NUESTRA CARTA
        </h1>

        <div
            style="width:80px;height:4px;background:#ffc107;margin:0 auto;"
            class="rounded-pill">
        </div>

        <p class="text-muted mt-3 fs-5">
            Sabores auténticos, ingredientes frescos y recetas tradicionales elaboradas con amor.
        </p>
    </div>

    <!-- MENSAJE DE ÉXITO -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4">
            <i class="fas fa-check-circle me-2"></i>

            <?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8'); ?>

            <?php unset($_SESSION['success']); ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>
        </div>
    <?php endif; ?>

    <!-- PRODUCTOS -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">

        <?php
        $hayProductos = false;

        foreach ($listaproductos as $producto):

            // No mostrar productos inactivos.
            if (!$producto->getActivo()) {
                continue;
            }

            $hayProductos = true;

            $id = (int) $producto->getId();

            $nombre = htmlspecialchars(
                $producto->getNombre(),
                ENT_QUOTES,
                'UTF-8'
            );

            $descripcion = htmlspecialchars(
                $producto->getDescripcion() ?? '',
                ENT_QUOTES,
                'UTF-8'
            );

            $precio = number_format(
                (float) $producto->getPrecio(),
                2,
                ',',
                '.'
            );

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
        ?>

            <div class="col d-flex">

                <div class="card producto-card w-100 shadow-sm border-0 rounded-4 overflow-hidden bg-white">

                    <!-- IMAGEN Y PRECIO -->
                    <div
                        class="position-relative overflow-hidden"
                        style="height:200px;">

                        <img
                            src="<?= htmlspecialchars($rutaImagen, ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= $nombre ?>"
                            class="w-100 h-100 producto-imagen"
                            onerror="this.onerror=null;this.src='<?= htmlspecialchars(url . 'Imagenes/default.png', ENT_QUOTES, 'UTF-8') ?>'">

                        <div
                            class="position-absolute bottom-0 start-0 bg-dark text-warning fw-bold px-3 py-2"
                            style="background:rgba(0,0,0,.75)!important;">

                            <?= $precio ?> €

                        </div>
                    </div>

                    <!-- INFORMACIÓN DEL PRODUCTO -->
                    <div class="card-body p-4 d-flex flex-column">

                        <h5 class="fw-bold text-dark">
                            <?= $nombre ?>
                        </h5>

                        <p class="text-muted flex-grow-1">
                            <?= $descripcion ?>
                        </p>

                        <div class="mt-auto">

                            <a
                                href="<?= url ?>?controller=producto&action=personalizar&id=<?= $id ?>"
                                class="btn btn-warning fw-bold w-100">

                                <i class="fas fa-sliders-h me-2"></i>
                                Personalizar producto

                            </a>

                        </div>

                    </div>
                </div>

            </div>

        <?php endforeach; ?>

    </div>

    <!-- MENSAJE SI NO HAY PRODUCTOS -->
    <?php if (!$hayProductos): ?>

        <div class="text-center py-5">
            <h4 class="fw-bold text-muted">
                No hay productos disponibles.
            </h4>

            <p class="text-muted">
                Vuelve a consultar nuestra carta más adelante.
            </p>
        </div>

    <?php endif; ?>

</div>

<style>
    .producto-card {
        transition: transform .3s, box-shadow .3s;
    }

    .producto-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,.1) !important;
    }

    .producto-imagen {
        height: 200px;
        object-fit: cover;
        transition: transform .5s;
    }

    .producto-card:hover .producto-imagen {
        transform: scale(1.05);
    }

    .btn-warning {
        background: #ffc107;
        border: none;
    }

    .btn-warning:hover {
        background: #e5ac00;
    }
</style>
