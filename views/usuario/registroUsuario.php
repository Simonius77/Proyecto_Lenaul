
<div class="container py-5">

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">

            <div class="card shadow border-0">

                <div class="card-body p-4 p-md-5">

                    <h2 class="text-center fw-bold mb-2">
                        Crear una cuenta
                    </h2>

                    <p class="text-center text-muted mb-4">
                        Regístrate en Lenault y disfruta de nuestra experiencia.
                    </p>

                    <!-- Mostrar errores -->
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form action="?controller=usuario&action=registro" method="POST">

                        <!-- Nombre -->
                        <div class="mb-3">
                            <label for="nombre" class="form-label">
                                Nombre completo *
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="nombre"
                                name="nombre"
                                placeholder="Introduce tu nombre"
                                value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>"
                                maxlength="100"
                                required
                            >
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                Correo electrónico *
                            </label>

                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                placeholder="ejemplo@correo.com"
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                maxlength="150"
                                required
                            >
                        </div>

                        <!-- Contraseña -->
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                Contraseña *
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                placeholder="Mínimo 8 caracteres"
                                minlength="8"
                                required
                            >

                            <div class="form-text">
                                La contraseña debe tener al menos 8 caracteres.
                            </div>
                        </div>

                        <!-- Teléfono -->
                        <div class="mb-3">
                            <label for="telefono" class="form-label">
                                Teléfono
                            </label>

                            <input
                                type="tel"
                                class="form-control"
                                id="telefono"
                                name="telefono"
                                placeholder="Introduce tu teléfono"
                                value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>"
                                maxlength="20"
                            >
                        </div>

                        <!-- Dirección -->
                        <div class="mb-3">
                            <label for="direccion" class="form-label">
                                Dirección
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="direccion"
                                name="direccion"
                                placeholder="Introduce tu dirección"
                                value="<?= htmlspecialchars($_POST['direccion'] ?? '') ?>"
                                maxlength="255"
                            >
                        </div>

                        <!-- Fecha de nacimiento -->
                        <div class="mb-4">
                            <label for="fecha_nacimiento" class="form-label">
                                Fecha de nacimiento
                            </label>

                            <input
                                type="date"
                                class="form-control"
                                id="fecha_nacimiento"
                                name="fecha_nacimiento"
                                value="<?= htmlspecialchars($_POST['fecha_nacimiento'] ?? '') ?>"
                            >
                        </div>

                        <!-- Botón de registro -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark btn-lg">
                                Crear cuenta
                            </button>
                        </div>

                    </form>

                    <hr class="my-4">

                    <p class="text-center mb-0">
                        ¿Ya tienes una cuenta?

                        <a href="?controller=usuario&action=login"
                           class="text-decoration-none fw-bold">
                            Inicia sesión
                        </a>
                    </p>

                </div>

            </div>

        </div>
    </div>

</div>
