
<?php
/* clase hija dependiente de producto */
require_once __DIR__ . '/Producto.php';

class Plato extends Producto
{
    public function getTipo(): string
    {
        return 'plato';
    }
}
