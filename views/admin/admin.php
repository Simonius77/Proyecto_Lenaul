
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <?php if (isset($_GET['controller']) && $_GET['controller'] === 'admin'): ?>
        <link rel="stylesheet" href="<?= url ?>css/admin.css">
    <?php endif; ?>

    <title>Panel de administración</title>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark p-3">
        <div class="container">
            <a class="navbar-brand" href="#">LENAULT ADMIN</a>

            <div class="navbar-nav">
                <a class="nav-link" href="#" data-view="inicio">Inicio</a>
                <a class="nav-link" href="#" data-view="pedidos">Pedidos</a>
                <a class="nav-link" href="#" data-view="usuarios">Usuarios</a>
                <a class="nav-link" href="#" data-view="productos">Productos</a>
            </div>
        </div>
    </nav>

    <main class="container mt-4">

        <section data-view="inicio">
            <h2>Inicio</h2>
            <p>Hola, bienvenido al panel de administración.</p>
        </section>

        <section data-view="pedidos" hidden>
            <h2>Pedidos</h2>
            <p>Sección de pedidos.</p>
        </section>

        <section data-view="usuarios" hidden>
            <h2>Usuarios</h2>
            <p>Sección de usuarios.</p>
        </section>

        <section data-view="productos" hidden>
            <h2>Productos</h2>

            <form id="formularioEliminarProducto" class="mb-4">
                <input
                    type="number"
                    id="productoId"
                    name="productoId"
                    placeholder="ID del producto a eliminar"
                    min="1"
                    required
                    class="form-control mb-2"
                >

                <button type="submit" class="btn btn-danger">
                    Eliminar producto
                </button>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Categoría</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Imagen</th>
                            <th>Activo</th>
                            <th>Tipo</th>
                        </tr>
                    </thead>

                    <tbody id="tablaProductos">
                        <!-- Los productos se cargarán desde JavaScript -->
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <script src="JS/adminView.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
