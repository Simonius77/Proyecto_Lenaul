<!--esta linea solo corrige un fallo del editor que no reconoce la variable $producto 
    como un objeto Producto, me lo ha pasado la IA por que el fallo me volvia loco-->
<?php
/** @var Producto $producto */
?>

<h1>Detalle del producto</h1>
<p><a href="?controller=producto">Volver al listado</a></p>

<p>
    <strong>Nombre:</strong>
    <?= htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8') ?>
</p>

<p>
    <strong>Descripción:</strong>
    <?= htmlspecialchars($producto->getDescripcion() ?? '', ENT_QUOTES, 'UTF-8') ?>
</p>

<p>
    <strong>Categoría ID:</strong>
    <?= (int) $producto->getCategoriaId() ?>
</p>

<p>
    <strong>Precio:</strong>
    <?= number_format((float) $producto->getPrecio(), 2, ',', '.') ?> €
</p>

<p>
    <strong>Imagen:</strong>
    <?= htmlspecialchars($producto->getImagen() ?? '', ENT_QUOTES, 'UTF-8') ?>
</p>

<p>
    <strong>Estado:</strong>
    <?= $producto->getActivo() ? 'Activo' : 'Inactivo' ?>
</p>

<p>
    <a
        href="?controller=producto&action=destroy&id=<?= (int) $producto->getId() ?>"
        onclick="return confirm('¿Seguro que quieres eliminar este producto?');"
    >Eliminar</a>
</p>

