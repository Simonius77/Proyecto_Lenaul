<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <a href="#" data-view="inicio">Inicio</a>
    <a href="#" data-view="pedidos">Pedidos</a>
    <a href="#" data-view="usuarios">Usuarios</a>

    <section data-view="inicio">
        <p>Hola bienvenido al panel de admin</p>
    </section>
    <section data-view="pedidos">
        <p>Seccion de pedidos</p>
    </section>
    <section data-view="usuarios">
        <p>Seccion de usuarios</p>
    </section>
    <section data-view="productos" hidden>
        <p>Seccion de productos</p>

        <form action="" id="eliminarProductos">
            <input type="text" id="productoId" name="productoId" placeholder="ID del producto a eliminar">
            <button type="submit">Eliminar producto</button>
        </form>
        <table id="productos"></table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Categoria</th>
                </tr>
            </thead>
            <tbody id="productos">
                <!-- Aquí se llenarán los productos dinámicamente -->
            </tbody>
        </table>

    </section>
    <!--enlazamos el js del panel de admin-->
    <script src="adminView.js"></script>
</body>
</html>