
<h1>Listado de Productos</h1>

<p>
    <a href="?controller=producto&action=create">Crear producto</a>
</p>

<table>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Categoría</th>
        <th>Precio</th>
        <th>Estado</th>
        <th>Acciones</th>
    </tr>

    <?php if (empty($productos)) { ?>
        <tr>
            <td colspan="6">No hay productos todavía.</td>
        </tr>
    <?php } ?>

    <?php foreach ($productos as $producto) { ?>
        <tr>
            <td><?= (int) $producto->getId() ?></td>

            <td>
                <?= htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8') ?>
            </td>

            <td><?= (int) $producto->getCategoriaId() ?></td>

            <td>
                <?= number_format((float) $producto->getPrecio(), 2, ',', '.') ?> €
            </td>

            <td>
                <?= $producto->getActivo() ? 'Activo' : 'Inactivo' ?>
            </td>

            <td>
                <a href="?controller=producto&action=show&id=<?= (int) $producto->getId() ?>">
                    Ver
                </a>

                <form
                    action="?controller=producto&action=destroy&id=<?= (int) $producto->getId() ?>"
                    method="POST"
                    style="display:inline"
                    onsubmit="return confirm('¿Seguro que quieres eliminar este producto?');"
                >
                    <button type="submit">Eliminar</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>
