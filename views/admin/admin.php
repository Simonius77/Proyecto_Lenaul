
<?php
// Comprobación de acceso al panel.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: ' . url);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Panel de administración | Lenaul</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Estilos del proyecto -->
    <link rel="stylesheet" href="<?= url ?>css/styles.css">

    <!-- Estilos del administrador -->
    <link rel="stylesheet" href="<?= url ?>css/admin.css">
</head>

<body>

    <!-- HEADER -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">

            <a class="navbar-brand" href="<?= url ?>">
                Lenaul
            </a>

            <div class="ms-auto d-flex align-items-center gap-3">

                <span class="text-white">
                    Panel de administración
                </span>

                <a href="<?= url ?>"
                   class="btn btn-outline-light btn-sm">
                    Volver a la web
                </a>

            </div>

        </nav>
    </header>


    <!-- CONTENIDO PRINCIPAL -->
    <main>

        <div class="container-fluid py-4">

            <!-- CABECERA -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">

                <div>
                    <h1>Panel de administración</h1>
                    <p class="text-muted mb-0">Gestión de Lenaul</p>
                </div>

            </div>


            <!-- MENÚ DE NAVEGACIÓN -->
            <nav class="mb-4">

                <div class="btn-group flex-wrap" role="group">

                    <button type="button" class="btn btn-primary" data-menu="inicio">
                        Inicio
                    </button>

                    <button type="button" class="btn btn-primary" data-menu="productos">
                        Productos
                    </button>

                    <button type="button" class="btn btn-primary" data-menu="usuarios">
                        Usuarios
                    </button>

                    <button type="button" class="btn btn-primary" data-menu="pedidos">
                        Pedidos
                    </button>

                </div>

            </nav>


            <!-- INICIO -->
            <section data-view="inicio">

                <div class="card shadow-sm p-4">

                    <h2>Bienvenido al administrador de Lenaul</h2>

                    <p>
                        Desde este panel puedes gestionar los productos,
                        usuarios y pedidos de tu restaurante.
                    </p>

                    <div class="row g-3 mt-3">

                        <div class="col-md-4">

                            <div class="card bg-light p-3 h-100">

                                <h3>Productos</h3>

                                <p>Gestiona los platos, bebidas y postres.</p>

                                <button type="button"
                                        class="btn btn-primary"
                                        data-menu="productos">
                                    Gestionar productos
                                </button>

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="card bg-light p-3 h-100">

                                <h3>Usuarios</h3>

                                <p>Administra las cuentas y sus permisos.</p>

                                <button type="button"
                                        class="btn btn-primary"
                                        data-menu="usuarios">
                                    Gestionar usuarios
                                </button>

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="card bg-light p-3 h-100">

                                <h3>Pedidos</h3>

                                <p>Consulta y administra los pedidos.</p>

                                <button type="button"
                                        class="btn btn-primary"
                                        data-menu="pedidos">
                                    Gestionar pedidos
                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            </section>


            <!-- PRODUCTOS -->
            <section data-view="productos" hidden>

                <h2 class="mb-4">Gestión de productos</h2>

                <!-- FORMULARIO DE PRODUCTOS -->
                <div class="card shadow-sm p-4 mb-4">

                    <h3 id="tituloFormulario">Crear producto</h3>

                    <form id="formularioProducto">

                        <input type="hidden" id="productoId">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label for="categoriaProducto" class="form-label">
                                    Categoría
                                </label>

                                <select id="categoriaProducto"
                                        class="form-select"
                                        required>

                                    <option value="">Selecciona una categoría</option>
                                    <option value="1">Entrantes</option>
                                    <option value="2">Primeros</option>
                                    <option value="3">Segundos</option>
                                    <option value="4">Postres</option>
                                    <option value="5">Bebidas</option>

                                </select>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label for="nombreProducto" class="form-label">
                                    Nombre
                                </label>

                                <input type="text"
                                       id="nombreProducto"
                                       class="form-control"
                                       maxlength="255"
                                       required>

                            </div>

                            <div class="col-12 mb-3">

                                <label for="descripcionProducto" class="form-label">
                                    Descripción
                                </label>

                                <textarea id="descripcionProducto"
                                          class="form-control"
                                          rows="3"></textarea>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label for="precioProducto" class="form-label">
                                    Precio (€)
                                </label>

                                <input type="number"
                                       id="precioProducto"
                                       class="form-control"
                                       min="0"
                                       step="0.01"
                                       required>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label for="imagenProducto" class="form-label">
                                    Imagen
                                </label>

                                <input type="text"
                                       id="imagenProducto"
                                       class="form-control"
                                       placeholder="Ruta de la imagen">

                            </div>

                            <div class="col-12 mb-3">

                                <div class="form-check">

                                    <input type="checkbox"
                                           id="activoProducto"
                                           class="form-check-input"
                                           checked>

                                    <label for="activoProducto"
                                           class="form-check-label">
                                        Producto activo
                                    </label>

                                </div>

                            </div>

                        </div>

                        <div class="mt-3">

                            <button type="submit"
                                    id="botonGuardarProducto"
                                    class="btn btn-success">
                                Crear producto
                            </button>

                            <button type="button"
                                    id="botonCancelarEdicion"
                                    class="btn btn-secondary"
                                    hidden>
                                Cancelar edición
                            </button>

                        </div>

                    </form>

                </div>


                <!-- TABLA DE PRODUCTOS -->
                <div class="card shadow-sm p-4">

                    <h3 class="mb-3">Listado de productos</h3>

                    <div class="table-responsive">

                        <table class="table table-striped table-hover align-middle">

                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Categoría</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Precio</th>
                                    <th>Imagen</th>
                                    <th>Activo</th>
                                    <th>Tipo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody id="tablaProductos">
                                <!-- Productos cargados mediante JavaScript -->
                            </tbody>

                        </table>

                    </div>

                </div>

            </section>


            <!-- USUARIOS -->
            <section data-view="usuarios" hidden>

                <h2 class="mb-4">Gestión de usuarios</h2>

                <!-- FORMULARIO DE USUARIOS -->
                <div class="card shadow-sm p-4 mb-4">

                    <h3 id="tituloFormularioUsuario">Crear usuario</h3>

                    <form id="formularioUsuario">

                        <input type="hidden" id="usuarioId">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label for="nombreUsuario" class="form-label">
                                    Nombre
                                </label>

                                <input type="text"
                                       id="nombreUsuario"
                                       class="form-control"
                                       maxlength="100"
                                       required>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label for="emailUsuario" class="form-label">
                                    Correo electrónico
                                </label>

                                <input type="email"
                                       id="emailUsuario"
                                       class="form-control"
                                       required>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label for="passwordUsuario" class="form-label">
                                    Contraseña
                                </label>

                                <input type="password"
                                       id="passwordUsuario"
                                       class="form-control"
                                       autocomplete="new-password">

                                <small class="text-muted">
                                    Obligatoria al crear. Al editar, déjala vacía
                                    para conservar la contraseña actual.
                                </small>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label for="telefonoUsuario" class="form-label">
                                    Teléfono
                                </label>

                                <input type="tel"
                                       id="telefonoUsuario"
                                       class="form-control"
                                       maxlength="20">

                            </div>

                            <div class="col-md-6 mb-3">

                                <label for="direccionUsuario" class="form-label">
                                    Dirección
                                </label>

                                <input type="text"
                                       id="direccionUsuario"
                                       class="form-control"
                                       maxlength="255">

                            </div>

                            <div class="col-md-6 mb-3">

                                <label for="fechaNacimientoUsuario" class="form-label">
                                    Fecha de nacimiento
                                </label>

                                <input type="date"
                                       id="fechaNacimientoUsuario"
                                       class="form-control"
                                       required>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label for="rolUsuario" class="form-label">
                                    Rol
                                </label>

                                <select id="rolUsuario"
                                        class="form-select"
                                        required>

                                    <option value="usuario">Usuario</option>
                                    <option value="admin">Administrador</option>

                                </select>

                            </div>

                        </div>

                        <div class="mt-3">

                            <button type="submit"
                                    id="botonGuardarUsuario"
                                    class="btn btn-success">
                                Crear usuario
                            </button>

                            <button type="button"
                                    id="botonCancelarUsuario"
                                    class="btn btn-secondary"
                                    hidden>
                                Cancelar edición
                            </button>

                        </div>

                    </form>

                </div>


                <!-- TABLA DE USUARIOS -->
                <div class="card shadow-sm p-4">

                    <h3 class="mb-3">Listado de usuarios</h3>

                    <div class="table-responsive">

                        <table class="table table-striped table-hover align-middle">

                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Dirección</th>
                                    <th>Fecha nacimiento</th>
                                    <th>Rol</th>
                                    <th>Fecha registro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody id="tablaUsuarios">
                                <!-- Usuarios cargados mediante JavaScript -->
                            </tbody>

                        </table>

                    </div>

                </div>

            </section>


            <!-- PEDIDOS -->
            <section data-view="pedidos" hidden>

                <h2 class="mb-4">Gestión de pedidos</h2>

                <div class="card shadow-sm p-4">

                    <h3 class="mb-3">Listado de pedidos</h3>

                    <div class="table-responsive">

                        <table class="table table-striped table-hover align-middle">

                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody id="tablaPedidos">
                                <!-- Los pedidos se cargarán mediante JavaScript -->
                            </tbody>

                        </table>

                    </div>

                    <p class="text-muted mt-3">
                        La gestión de pedidos necesita su controlador y sus
                        operaciones API conectadas a la base de datos.
                    </p>

                </div>

            </section>

        </div>

    </main>


    <!-- FOOTER -->
    <footer class="bg-dark text-white text-center py-3 mt-5">

        <p class="mb-0">
            &copy; <?= date("Y") ?> Lenault. Todos los derechos reservados.
        </p>

    </footer>


    <!-- JavaScript -->
    <script src="<?= url ?>JS/adminView.js?version=<?= time() ?>"></script>

</body>
</html>
