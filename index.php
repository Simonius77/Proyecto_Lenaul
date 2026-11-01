<?php

include_once("config/parameters.php");

function showError($code, $message)
{
    http_response_code($code);

    $errorCode = $code;
    $errorMessage = $message;
    $view = "views/error.php";

    include_once "views/main.php";
    exit;
}

// Si no se indica controlador, cargar el predeterminado.
if (!isset($_GET['controller']) || $_GET['controller'] === '') {
    header("Location: " . url . "?controller=" . default_controller);
    exit;
}

// Normalizar el nombre del controlador.
$controllerParam = strtolower(trim($_GET['controller']));

$nombre_controller = $controllerParam . "Controller";
$controllerFile = "controllers/" . $nombre_controller . ".php";

// Comprobar que existe el archivo.
if (!file_exists($controllerFile)) {
    showError(404, "No existe el controller " . $nombre_controller);
}

include_once $controllerFile;

// Comprobar que existe la clase.
if (!class_exists($nombre_controller)) {
    showError(500, "El fichero existe pero no contiene la clase " . $nombre_controller);
}

// Crear el controlador.
$controller = new $nombre_controller();

// Obtener la acción.
$action = default_action;

if (isset($_GET['action']) && $_GET['action'] !== '') {
    $action = strtolower(trim($_GET['action']));
}

// Comprobar que existe la acción.
if (!is_callable([$controller, $action])) {
    showError(
        404,
        "No existe la action \"" . $action . "\" en " . $nombre_controller
    );
}

// Ejecutar la acción.
$controller->$action();
