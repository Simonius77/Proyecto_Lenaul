<h1>Crear producto</h1>
<p><a href="?controller=producto">Volver al listado</a></p>

<form action="?controller=producto&action=store" method="POST">
    <label>
        Nombre
        <input
            type="text"
            name="nombre"
            id="nombre"
            required
            maxlength="150"
        >
    </label>

    <br><br>

    <label>
        ID de categoría
        <input
            type="number"
            name="categoria_id"
            id="categoria_id"
            min="1"
            required
        >
    </label>

    <br><br>

    <label>
        Descripción
        <textarea
            name="descripcion"
            id="descripcion"
        ></textarea>
    </label>

    <br><br>

    <label>
        Precio
        <input
            type="number"
            name="precio"
            id="precio"
            step="0.01"
            min="0"
            required
        >
    </label>

    <br><br>

    <label>
        Imagen (ruta o nombre del archivo)
        <input
            type="text"
            name="imagen"
            id="imagen"
            maxlength="255"
        >
    </label>

    <br><br>

    <label>
        <input
            type="checkbox"
            name="activo"
            value="1"
            checked
        >
        Producto activo
    </label>

    <br><br>

    <button type="submit">Guardar</button>
</form>
