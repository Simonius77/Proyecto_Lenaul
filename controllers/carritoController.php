
<?php

require_once __DIR__ . '/../models/IngredienteDAO.php';

class carritoController
{
    
        // Mostrar el carrito y calcular los importes.
    
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $itemsCarrito = $_SESSION['carrito'] ?? [];

        $totalUnidades = 0;
        $subtotalProductos = 0;

        foreach ($itemsCarrito as $indice => $item) {

            $cantidad = (int) ($item['cantidad'] ?? 1);
            $precioUnitario = (float) ($item['subtotal'] ?? $item['precio'] ?? 0);

            $totalUnidades += $cantidad;

            $subtotalLinea = $precioUnitario * $cantidad;
            $subtotalProductos += $subtotalLinea;

            // Obtener los nombres de los ingredientes originales.
            $ingredientesNombres = [];

            $ingredientesOriginales =
                IngredienteDAO::getByProductoId(
                    (int) $item['producto_id']
                );

            $idsSeleccionados = $item['ingredientes'] ?? [];

            foreach ($ingredientesOriginales as $ingrediente) {
                if (in_array(
                    (int) $ingrediente->id,
                    array_map('intval', $idsSeleccionados),
                    true
                )) {
                    $ingredientesNombres[] = $ingrediente->nombre;
                }
            }

            // Preparar los datos para la vista.
            $itemsCarrito[$indice]['nombre'] =
                $item['nombre'] ?? 'Producto';

            $itemsCarrito[$indice]['cantidad'] = $cantidad;

            $itemsCarrito[$indice]['precio_unitario'] =
                $precioUnitario;

            $itemsCarrito[$indice]['subtotal_linea'] =
                $subtotalLinea;

            $itemsCarrito[$indice]['ingredientes_nombres'] =
                $ingredientesNombres;

            $itemsCarrito[$indice]['extras'] =
                $item['extras'] ?? [];
        }

        // Descuentos disponibles.
        $codigosDescuento = [
            'LENAULT10' => [
                'porcentaje' => 10,
                'descripcion' => 'Descuento del 10%'
            ],
            'BIENVENIDA15' => [
                'porcentaje' => 15,
                'descripcion' => 'Descuento del 15%'
            ]
        ];

        $descuentoAplicado = $_SESSION['descuento'] ?? null;

        $porcentajeDescuento = 0;
        $importeDescuento = 0;

        if ($descuentoAplicado !== null) {
            $porcentajeDescuento =
                (float) $descuentoAplicado['porcentaje'];

            $importeDescuento =
                $subtotalProductos * ($porcentajeDescuento / 100);
        }

        // Calcular base imponible e IVA.
        $baseImponible = $subtotalProductos - $importeDescuento;

        $porcentajeIVA = 10;
        $importeIVA = $baseImponible * ($porcentajeIVA / 100);

        $totalFinal = $baseImponible + $importeIVA;

        // Cargar la vista dentro de la estructura principal.
        $view = 'productos/carrito.php';

        require_once __DIR__ . '/../views/main.php';
    }


    
        // Actualizar la cantidad de un producto.
     
    public function actualizarCantidad()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $indice = filter_input(
                INPUT_POST,
                'indice',
                FILTER_VALIDATE_INT
            );

            $cantidad = filter_input(
                INPUT_POST,
                'cantidad',
                FILTER_VALIDATE_INT
            );

            if (
                $indice !== false &&
                $indice !== null &&
                $cantidad !== false &&
                $cantidad >= 1 &&
                $cantidad <= 20 &&
                isset($_SESSION['carrito'][$indice])
            ) {
                $_SESSION['carrito'][$indice]['cantidad'] = $cantidad;
            }
        }

        header('Location: ?controller=carrito&action=index');
        exit;
    }


    
        // Eliminar un producto del carrito.
     
    public function eliminar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $indice = filter_input(
                INPUT_POST,
                'indice',
                FILTER_VALIDATE_INT
            );

            if (
                $indice !== false &&
                $indice !== null &&
                isset($_SESSION['carrito'][$indice])
            ) {
                unset($_SESSION['carrito'][$indice]);

                $_SESSION['carrito'] =
                    array_values($_SESSION['carrito']);
            }
        }

        header('Location: ?controller=carrito&action=index');
        exit;
    }


    
        // Vaciar completamente el carrito.
     
    public function vaciar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['carrito'] = [];

            unset($_SESSION['descuento']);
        }

        header('Location: ?controller=carrito&action=index');
        exit;
    }


    
        // Aplicar un código de descuento.
     
    public function aplicarDescuento()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $codigo = strtoupper(
                trim($_POST['codigo_descuento'] ?? '')
            );

            $codigosDescuento = [
                'LENAULT10' => [
                    'porcentaje' => 10,
                    'descripcion' => 'Descuento del 10%'
                ],
                'BIENVENIDA15' => [
                    'porcentaje' => 15,
                    'descripcion' => 'Descuento del 15%'
                ]
            ];

            if (empty($_SESSION['carrito'])) {

                $_SESSION['error'] =
                    'No puedes aplicar un descuento con el carrito vacío.';

            } elseif (isset($codigosDescuento[$codigo])) {

                $_SESSION['descuento'] = [
                    'codigo' => $codigo,
                    'porcentaje' =>
                        $codigosDescuento[$codigo]['porcentaje'],
                    'descripcion' =>
                        $codigosDescuento[$codigo]['descripcion']
                ];

                $_SESSION['success'] =
                    'Descuento aplicado correctamente.';

            } else {

                $_SESSION['error'] =
                    'El código de descuento no es válido.';
            }
        }

        header('Location: ?controller=carrito&action=index');
        exit;
    }


    
        // Quitar el descuento aplicado.
     
    public function quitarDescuento()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            unset($_SESSION['descuento']);

            $_SESSION['success'] =
                'Se ha eliminado el descuento.';
        }

        header('Location: ?controller=carrito&action=index');
        exit;
    }
}
