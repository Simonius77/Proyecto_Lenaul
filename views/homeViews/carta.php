
<?php
// Comprobamos que el controlador haya enviado los productos.
if (!isset($listaproductos) || !is_array($listaproductos)) {
    $listaproductos = [];
}

// Iniciamos la sesión si todavía no está iniciada.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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

            <?= htmlspecialchars(
                $_SESSION['success'],
                ENT_QUOTES,
                'UTF-8'
            ); ?>

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
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4">

            <i class="fas fa-exclamation-circle me-2"></i>

            <?= htmlspecialchars(
                $_SESSION['error'],
                ENT_QUOTES,
                'UTF-8'
            ); ?>

            <?php unset($_SESSION['error']); ?>

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

            // Ruta de la imagen.
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

            $rutaImagenSegura = htmlspecialchars(
                $rutaImagen,
                ENT_QUOTES,
                'UTF-8'
            );

            $imagenPorDefecto = htmlspecialchars(
                url . 'Imagenes/default.png',
                ENT_QUOTES,
                'UTF-8'
            );
        ?>

            <div class="col d-flex">

                <div class="card producto-card w-100 shadow-sm border-0 rounded-4 overflow-hidden bg-white">

                    <!-- IMAGEN Y PRECIO -->
                    
                    <div class="contenedor-imagen-producto">

                        <img
                            src="<?= $rutaImagenSegura ?>"
                            alt="<?= $nombre ?>"
                            class="producto-imagen"
                            onerror="this.onerror=null;this.src='<?= $imagenPorDefecto ?>'">

                        <div class="precio-producto">
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

                        <!-- BOTONES -->
                        <div class="mt-auto d-flex flex-column gap-2">

                            <!-- PERSONALIZAR PRODUCTO -->
                            <a
                                href="<?= url ?>?controller=producto&action=personalizar&id=<?= $id ?>"
                                class="btn btn-warning fw-bold w-100">

                                <i class="fas fa-sliders-h me-2"></i>
                                Personalizar producto

                            </a>

                            <!-- AÑADIR DIRECTAMENTE AL CARRITO -->
                            <form
                                action="<?= url ?>?controller=producto&action=agregarCarrito"
                                method="POST">

                                <input
                                    type="hidden"
                                    name="producto_id"
                                    value="<?= $id ?>">

                                <input
                                    type="hidden"
                                    name="modo"
                                    value="directo">

                                <button
                                    type="submit"
                                    class="btn btn-success fw-bold w-100">

                                    <i class="fas fa-cart-plus me-2"></i>
                                    Añadir al carrito

                                </button>

                            </form>

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
    
    /* Tarjetas */
    .producto-card {
        transition: transform .3s, box-shadow .3s;
    }

    /* Efecto al pasar el ratón */
    .producto-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,.1) !important;
    }

    /* Contenedor de imagen */
    .contenedor-imagen-producto {
        width: 100%;
        height: 220px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background-color: #f8f8f8;
    }

    /* Imagen completa */
    .producto-imagen {
        display: block;
        width: 100%;
        height: 100%;
        max-width: 100%;
        object-fit: contain !important;
        object-position: center;
        transform: none !important;
        transition: none;
    }

    /* Precio */
    .precio-producto {
        position: absolute;
        bottom: 0;
        left: 0;
        background: rgba(0,0,0,.75);
        color: #ffc107;
        font-weight: bold;
        padding: 8px 16px;
    }

    /* Botones */
    .btn-warning {
        background: #ffc107;
        border: none;
    }

    .btn-warning:hover {
        background: #e5ac00;
    }

    .btn-success {
        border: none;
    }

</style>
