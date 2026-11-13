
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">

            <div class="card shadow border-0">
                <div class="card-body p-4 p-md-5">

                    <h2 class="text-center mb-2">Iniciar sesión</h2>

                    <p class="text-center text-muted mb-4">
                        Accede a tu cuenta de Lenault
                    </p>

                    <?php if (!empty($mensaje)): ?>
                        <div class="alert alert-success">
                            <?= htmlspecialchars($mensaje) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form action="?controller=usuario&action=login" method="POST">

                        <div class="mb-3">
                            <label for="username" class="form-label">
                                Usuario o correo electrónico
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="username"
                                name="username"
                                placeholder="Introduce tu usuario o email"
                                value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                                required
                            >
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">
                                Contraseña
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                placeholder="Introduce tu contraseña"
                                required
                            >
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark btn-lg">
                                Iniciar sesión
                            </button>
                        </div>

                    </form>

                    <hr class="my-4">

                    <p class="text-center mb-0">
                        ¿Todavía no tienes cuenta?
                        <a href="?controller=usuario&action=registro"
                           class="text-decoration-none fw-bold">
                            Regístrate aquí
                        </a>
                    </p>

                </div>
            </div>

        </div>
    </div>
</div>
