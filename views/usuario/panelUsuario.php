
<div class="container py-5">

    <div class="row justify-content-center">
        <div class="col-lg-9">

            <!-- Cabecera del panel -->
            <div class="text-center mb-5">
                <h2 class="fw-bold">
                    ¡Bienvenido/a, <?= htmlspecialchars($usuario->getNombre()) ?>!
                </h2>

                <p class="text-muted">
                    Este es tu espacio personal en Lenault.
                </p>
            </div>

            <div class="row g-4">

                <!-- Información personal -->
                <div class="col-md-7">

                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-4">

                            <h4 class="mb-4">
                                <i class="bi bi-person-circle me-2"></i>
                                Mis datos
                            </h4>

                            <div class="mb-3">
                                <label class="text-muted small">Nombre</label>
                                <p class="fw-semibold">
                                    <?= htmlspecialchars($usuario->getNombre()) ?>
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="text-muted small">Correo electrónico</label>
                                <p class="fw-semibold">
                                    <?= htmlspecialchars($usuario->getEmail()) ?>
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="text-muted small">Teléfono</label>
                                <p class="fw-semibold">
                                    <?= htmlspecialchars($usuario->getTelefono() ?? 'No indicado') ?>
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="text-muted small">Dirección</label>
                                <p class="fw-semibold">
                                    <?= htmlspecialchars($usuario->getDireccion() ?? 'No indicada') ?>
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="text-muted small">Fecha de nacimiento</label>
                                <p class="fw-semibold">
                                    <?= htmlspecialchars($usuario->getFecha_nacimiento() ?? 'No indicada') ?>
                                </p>
                            </div>

                            <div>
                                <label class="text-muted small">Fecha de registro</label>
                                <p class="fw-semibold">
                                    <?= htmlspecialchars($usuario->getFecha_registro() ?? 'No disponible') ?>
                                </p>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- Opciones del usuario -->
                <div class="col-md-5">

                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body p-4">

                            <h4 class="mb-3">
                                Mi cuenta
                            </h4>

                            <p class="text-muted">
                                Desde aquí podrás gestionar tu cuenta.
                            </p>

                            <div class="d-grid gap-3">

                                <a href="?controller=home&action=index"
                                   class="btn btn-outline-dark">
                                    <i class="bi bi-house me-2"></i>
                                    Volver al inicio
                                </a>

                                <a href="?controller=carta&action=index"
                                   class="btn btn-outline-dark">
                                    <i class="bi bi-card-list me-2"></i>
                                    Ver nuestra carta
                                </a>

                            </div>

                        </div>
                    </div>

                    <!-- Cerrar sesión -->
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">

                            <h5 class="mb-3">Cerrar sesión</h5>

                            <p class="text-muted">
                                ¿Has terminado? Puedes salir de tu cuenta.
                            </p>

                            <div class="d-grid">
                                <a href="?controller=usuario&action=logout"
                                   class="btn btn-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>
                                    Cerrar sesión
                                </a>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

</div>
