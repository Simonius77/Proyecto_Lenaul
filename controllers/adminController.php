
<?php

// Controlador para la vista del panel de administración
class adminController
{
    public function index()
    {
        // Ruta absoluta de la vista del administrador
        $view = __DIR__ . '/../views/admin/admin.php';

        // Comprobar que existe la vista
        if (!file_exists($view)) {
            die("No se encuentra la vista: " . $view);
        }

        // Cargar la plantilla principal
        $view = __DIR__ . '/../views/admin/admin.php';
        require_once $view;
    }
}
