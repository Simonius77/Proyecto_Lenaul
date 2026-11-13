
<?php

include_once __DIR__ . '/../models/UsuarioDAO.php';

class usuarioController {

    // Iniciar sesión si todavía no está iniciada
    private function iniciarSesion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Guardar los datos del usuario en la sesión
    private function guardarSesion($usuario) {

        session_regenerate_id(true);

        $_SESSION['usuario_id'] = $usuario->getId();
        $_SESSION['usuario_nombre'] = $usuario->getNombre();
        $_SESSION['usuario_email'] = $usuario->getEmail();
        $_SESSION['usuario_rol'] = $usuario->getRol();
    }

    // Registrar usuario
    public function registro() {

        $this->iniciarSesion();

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $telefono = trim($_POST['telefono'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;

            if ($nombre === '' || $email === '' || $password === '') {

                $error = 'Completa todos los campos obligatorios.';

            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

                $error = 'Introduce un correo electrónico válido.';

            } elseif (strlen($password) < 8) {

                $error = 'La contraseña debe tener al menos 8 caracteres.';

            } elseif (UsuarioDAO::findByEmail($email) !== null) {

                $error = 'Ya existe una cuenta con ese correo electrónico.';

            } else {

                if ($fecha_nacimiento === '') {
                    $fecha_nacimiento = null;
                }

                $resultado = UsuarioDAO::insertUsuario(
                    $nombre,
                    $email,
                    $password,
                    $telefono,
                    $direccion,
                    $fecha_nacimiento
                );

                if ($resultado) {

                    // Buscar el usuario recién registrado
                    $usuario = UsuarioDAO::login($email, $password);

                    if ($usuario) {

                        // Iniciar sesión automáticamente
                        $this->guardarSesion($usuario);

                        // Redirigir al Home
                        header('Location: ' . url . '?controller=home&action=index');
                        exit;

                    } else {

                        $error = 'La cuenta se ha creado, pero no se pudo iniciar sesión.';
                    }

                } else {

                    $error = 'No se ha podido completar el registro.';
                }
            }
        }

        $view = __DIR__ . '/../views/usuario/registroUsuario.php';
        require __DIR__ . '/../views/main.php';
    }

    // Iniciar sesión
    public function login() {

        $this->iniciarSesion();

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($username === '' || $password === '') {

                $error = 'Introduce tu usuario o correo y tu contraseña.';

            } else {

                $usuario = UsuarioDAO::login($username, $password);

                if ($usuario) {

                    // Guardar la sesión
                    $this->guardarSesion($usuario);

                    // Redirigir al Home
                    header('Location: ' . url . '?controller=home&action=index');
                    exit;

                } else {

                    $error = 'El usuario o la contraseña no son correctos.';
                }
            }
        }

        $view = __DIR__ . '/../views/usuario/loginUsuario.php';
        require __DIR__ . '/../views/main.php';
    }

    // Mostrar panel de usuario
    public function panel() {

        $this->iniciarSesion();

        // Comprobar que hay una sesión iniciada
        if (!isset($_SESSION['usuario_id'])) {

            header('Location: ' . url . '?controller=usuario&action=login');
            exit;
        }

        // Obtener los datos del usuario
        $usuario = UsuarioDAO::getUsuarioByID($_SESSION['usuario_id']);

        if (!$usuario) {

            $_SESSION = [];
            session_destroy();

            header('Location: ' . url . '?controller=usuario&action=login');
            exit;
        }

        $view = __DIR__ . '/../views/usuario/panelUsuario.php';
        require __DIR__ . '/../views/main.php';
    }

    // Cerrar sesión
    public function logout() {

        $this->iniciarSesion();

        // Vaciar los datos de sesión
        $_SESSION = [];

        // Eliminar la cookie de sesión
        if (ini_get('session.use_cookies')) {

            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Destruir la sesión
        session_destroy();

        // Volver al Home
        header('Location: ' . url . '?controller=home&action=index');
        exit;
    }
}

?>

