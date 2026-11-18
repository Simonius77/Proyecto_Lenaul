
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Barra de navegación principal -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark py-3 shadow-sm">
    <div class="container">

        <!-- Logo -->
        <a class="navbar-brand fw-bold font-monospace text-warning fs-3"
           href="<?= url ?>?controller=home&action=index">

            <span class="text-white">LENAULT</span>

        </a>

        <!-- Botón responsive -->
        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav"
                aria-controls="navbarNav"
                aria-expanded="false"
                aria-label="Toggle navigation">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <!-- Menú izquierdo -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link active"
                       href="<?= url ?>?controller=home&action=index">
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link"
                       href="<?= url ?>?controller=carta&action=index">
                        Carta
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link"
                       href="<?= url ?>?controller=home&action=aboutus">
                        Quiénes Somos
                    </a>
                </li>

            </ul>


            <!-- Menú derecho -->
            <ul class="navbar-nav ms-auto align-items-center">

                <!-- Carrito -->
                <li class="nav-item me-3">

                    <a class="nav-link btn btn-outline-warning px-3 py-1 position-relative text-warning border-warning"
                       href="<?= url ?>?controller=carrito&action=index">

                        <i class="fas fa-shopping-cart me-1"></i>
                        Carrito

                        <?php
                        $cart_count = 0;

                        if (
                            isset($_SESSION['carrito']) &&
                            !empty($_SESSION['carrito'])
                        ) {
                            foreach ($_SESSION['carrito'] as $item) {

                                $cart_count += (int) (
                                    $item['cantidad'] ?? 0
                                );

                            }
                        }

                        if ($cart_count > 0):
                        ?>

                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">

                                <?= $cart_count; ?>

                            </span>

                        <?php endif; ?>

                    </a>

                </li>


                <!-- Usuario / My Lenault -->
                <?php if (isset($_SESSION['usuario_id'])): ?>

                    <li class="nav-item">

                        <a class="nav-link btn btn-warning text-dark px-3 fw-bold border-0"
                           href="<?= url ?>?controller=usuario&action=panel"
                           style="background: #ffc107;">

                            <i class="fas fa-user-circle me-1"></i>

                            <?= htmlspecialchars(
                                $_SESSION['usuario_nombre'] ?? 'Usuario',
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>

                        </a>

                    </li>


                    <!-- Cerrar sesión -->
                    <li class="nav-item">

                        <a class="nav-link text-white"
                           href="<?= url ?>?controller=usuario&action=logout">

                            <i class="fas fa-sign-out-alt me-1"></i>
                            Cerrar sesión

                        </a>

                    </li>

                <?php else: ?>


                    <!-- Usuario sin iniciar sesión -->
                    <li class="nav-item">

                        <a class="nav-link btn btn-warning text-dark px-3 fw-bold border-0"
                           href="<?= url ?>?controller=usuario&action=login"
                           style="background: #ffc107;">

                            <i class="fas fa-user me-1"></i>
                            My Lenault

                        </a>

                    </li>

                <?php endif; ?>

            </ul>

        </div>
    </div>
</nav>


<!-- Font Awesome -->
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
