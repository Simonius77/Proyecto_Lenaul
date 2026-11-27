
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        crossorigin="anonymous"
    >

    <!-- CSS general -->
    <link rel="stylesheet" href="<?= url ?>css/styles.css?v=2">

    <!-- CSS exclusivo del administrador -->
    <?php if (isset($_GET['controller']) && $_GET['controller'] === 'admin'): ?>
        <link rel="stylesheet" href="<?= url ?>css/admin.css?v=2">
    <?php endif; ?>

    <title>Lenaul</title>
</head>

<body>

    <!-- Cabecera y navegacion -->
    <header>
        <?php include_once __DIR__ . '/partials/navbar.php'; ?>
    </header>

    <!-- Contenido principal -->
    <main>
        <section>
            <?php
            if (isset($view)) {

                // Si la ruta es absoluta, utilizarla directamente.
                if (preg_match('~^(?:[A-Za-z]:[\\\\/]|/)~', $view)) {
                    $rutaVista = $view;
                }

                // Si comienza por "views/", buscar desde la raíz del proyecto.
                elseif (strpos($view, 'views/') === 0) {
                    $rutaVista = dirname(__DIR__) . DIRECTORY_SEPARATOR . $view;
                }

                // En cualquier otro caso, buscar desde la carpeta views.
                else {
                    $rutaVista = __DIR__ . DIRECTORY_SEPARATOR
                        . ltrim($view, '/\\');
                }

                // Comprobar y cargar la vista.
                if (file_exists($rutaVista)) {
                    require $rutaVista;
                } else {
                    echo "ERROR: No se encuentra la vista: "
                        . htmlspecialchars($rutaVista, ENT_QUOTES, 'UTF-8');
                }

            } else {
                echo "ERROR: No se ha definido ninguna vista.";
            }
            ?>
        </section>
    </main>

    <!-- Pie de pagina -->
    <?php include_once __DIR__ . '/partials/footer.php'; ?>

    <!-- Bootstrap JavaScript -->
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous">
    </script>

</body>
</html>
