
/* =====================================================
   FUNCIONES GENERALES
===================================================== */
console.log("VERSION NUEVA DE ADMINVIEW CARGADA");

// Construir URL de las API.

function crearApiUrl(controlador) {
    return new URL(
        `?controller=${encodeURIComponent(controlador)}&action=api`,
        window.location.origin + "/Proyecto_Lenaul/"
    ).toString();
}


// Procesar respuestas JSON.
async function procesarRespuesta(respuesta) {
    let datos;

    try {
        datos = await respuesta.json();
    } catch (error) {
        throw new Error(
            "El servidor no ha devuelto un JSON válido. Comprueba el controlador PHP."
        );
    }

    if (!respuesta.ok || !datos.success) {
        throw new Error(
            datos.mensaje ||
            datos.error ||
            `Error del servidor (${respuesta.status}).`
        );
    }

    return datos;
}

// Realizar peticiones a las API.
async function realizarPeticion(url, metodo = "GET", datos = null) {
    const opciones = {
        method: metodo,
        headers: {
            "Accept": "application/json"
        }
    };

    if (datos !== null) {
        opciones.headers["Content-Type"] = "application/json";
        opciones.body = JSON.stringify(datos);
    }

    const respuesta = await fetch(url, opciones);

    return await procesarRespuesta(respuesta);
}

// Crear una celda de tabla de forma segura.
function crearCelda(valor) {
    const celda = document.createElement("td");
    celda.textContent = valor ?? "";
    return celda;
}


/* =====================================================
   ADMINISTRACIÓN DE VISTAS
===================================================== */

class AdministrarVistas {

    constructor() {
        this.enlaces = [];
        this.secciones = [];
        this.vista = "inicio";

        this.productos = new Productos();
        this.usuarios = new Usuarios();
    }

    inicializar() {

        this.enlaces = document.querySelectorAll("[data-menu]");
        this.secciones = document.querySelectorAll("section[data-view]");

        // Eventos de navegación.
        this.enlaces.forEach(enlace => {

            enlace.addEventListener("click", () => {

                this.vista = enlace.dataset.menu;

                this.mostrarVista();

            });

        });

        // Mostrar inicio.
        this.mostrarVista();

        // Inicializar CRUD.
        this.productos.inicializar();
        this.usuarios.inicializar();
    }

    mostrarVista() {

        this.secciones.forEach(seccion => {

            seccion.hidden = seccion.dataset.view !== this.vista;

        });

        this.enlaces.forEach(enlace => {

            const activo = enlace.dataset.menu === this.vista;

            enlace.classList.toggle("active", activo);

            if (enlace.classList.contains("btn")) {
                enlace.classList.toggle("btn-dark", activo);
                enlace.classList.toggle("btn-primary", !activo);
            }

        });
    }

}


/* =====================================================
   GESTIÓN DE PRODUCTOS
===================================================== */

class Productos {

    constructor() {
        this.productos = [];
        this.apiUrl = crearApiUrl("producto");
    }

    inicializar() {

        const formulario = document.querySelector("#formularioProducto");

        if (formulario) {

            formulario.addEventListener("submit", e => {
                this.guardarProducto(e);
            });

        }

        const botonCancelar = document.querySelector("#botonCancelarEdicion");

        if (botonCancelar) {

            botonCancelar.addEventListener("click", () => {
                this.limpiarFormulario();
            });

        }

        this.obtenerProductos();
    }

    // Obtener productos.
    async obtenerProductos() {

        try {

            const datos = await realizarPeticion(
                `${this.apiUrl}&funcion=todos`
            );

            this.productos = datos.productos || [];

            this.mostrarProductos();

        } catch (error) {

            console.error("Error al cargar productos:", error);

            const tabla = document.querySelector("#tablaProductos");

            if (tabla) {
                tabla.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-danger">
                            No se pudieron cargar los productos.
                        </td>
                    </tr>
                `;
            }

            console.error(error.message);
        }
    }

    // Nombre de categoría.
    obtenerNombreCategoria(id) {

        const categorias = {
            1: "Entrantes",
            2: "Primeros",
            3: "Segundos",
            4: "Postres",
            5: "Bebidas"
        };

        return categorias[id] || "Desconocida";
    }

    // Comprobar estado activo.
    esActivo(valor) {

        return valor === true ||
            valor === 1 ||
            valor === "1";
    }

    // Mostrar productos.
    mostrarProductos() {

        const tabla = document.querySelector("#tablaProductos");

        if (!tabla) return;

        tabla.innerHTML = "";

        if (this.productos.length === 0) {

            const fila = document.createElement("tr");
            const celda = crearCelda("No hay productos registrados.");

            celda.colSpan = 9;
            fila.appendChild(celda);
            tabla.appendChild(fila);

            return;
        }

        this.productos.forEach(producto => {

            const fila = document.createElement("tr");

            const valores = [
                producto.id,
                this.obtenerNombreCategoria(producto.categoria_id),
                producto.nombre,
                producto.descripcion,
                `${Number(producto.precio).toFixed(2)} €`,
                producto.imagen,
                this.esActivo(producto.activo) ? "Sí" : "No",
                producto.tipo
            ];

            valores.forEach(valor => {
                fila.appendChild(crearCelda(valor));
            });

            const acciones = document.createElement("td");

            // Editar.
            const botonEditar = document.createElement("button");

            botonEditar.type = "button";
            botonEditar.textContent = "Editar";
            botonEditar.className = "btn btn-warning btn-sm me-2";

            botonEditar.addEventListener("click", () => {
                this.editarProducto(producto.id);
            });

            // Eliminar.
            const botonEliminar = document.createElement("button");

            botonEliminar.type = "button";
            botonEliminar.textContent = "Eliminar";
            botonEliminar.className = "btn btn-danger btn-sm";

            botonEliminar.addEventListener("click", () => {
                this.eliminarProducto(producto.id);
            });

            acciones.append(botonEditar, botonEliminar);

            fila.appendChild(acciones);
            tabla.appendChild(fila);

        });
    }

    // Guardar producto.
    async guardarProducto(e) {

        e.preventDefault();

        const formulario = document.querySelector("#formularioProducto");

        const id = document.querySelector("#productoId").value;

        const producto = {
            categoria_id: Number(
                document.querySelector("#categoriaProducto").value
            ),
            nombre: document.querySelector("#nombreProducto").value.trim(),
            descripcion: document.querySelector("#descripcionProducto").value.trim(),
            precio: Number(
                document.querySelector("#precioProducto").value
            ),
            imagen: document.querySelector("#imagenProducto").value.trim(),
            activo: document.querySelector("#activoProducto").checked
        };

        if (!formulario.checkValidity()) {
            formulario.reportValidity();
            return;
        }

        if (
            !producto.categoria_id ||
            !producto.nombre ||
            !Number.isFinite(producto.precio) ||
            producto.precio < 0
        ) {
            alert("Comprueba los datos del producto.");
            return;
        }

        const editando = id !== "";

        try {

            const datos = await realizarPeticion(
                `${this.apiUrl}&funcion=${editando ? "actualizar" : "crear"}`,
                editando ? "PUT" : "POST",
                editando
                    ? { ...producto, id: Number(id) }
                    : producto
            );

            alert(datos.mensaje || "Producto guardado correctamente.");

            this.limpiarFormulario();

            await this.obtenerProductos();

        } catch (error) {

            console.error("Error al guardar producto:", error);
            alert(error.message);

        }
    }

    // Editar producto.
    editarProducto(id) {

        const producto = this.productos.find(
            p => Number(p.id) === Number(id)
        );

        if (!producto) {
            alert("No se ha encontrado el producto.");
            return;
        }

        document.querySelector("#productoId").value = producto.id;

        document.querySelector("#categoriaProducto").value =
            producto.categoria_id;

        document.querySelector("#nombreProducto").value =
            producto.nombre ?? "";

        document.querySelector("#descripcionProducto").value =
            producto.descripcion ?? "";

        document.querySelector("#precioProducto").value =
            producto.precio;

        document.querySelector("#imagenProducto").value =
            producto.imagen ?? "";

        document.querySelector("#activoProducto").checked =
            this.esActivo(producto.activo);

        document.querySelector("#tituloFormulario").textContent =
            "Editar producto";

        document.querySelector("#botonGuardarProducto").textContent =
            "Guardar cambios";

        document.querySelector("#botonCancelarEdicion").hidden = false;

        // Cambiar a la sección productos.
        const enlaceProductos = document.querySelector(
            '[data-menu="productos"]'
        );

        if (enlaceProductos) {
            enlaceProductos.click();
        }

        document.querySelector("#formularioProducto").scrollIntoView({
            behavior: "smooth",
            block: "start"
        });
    }

    // Limpiar formulario.
    limpiarFormulario() {

        const formulario = document.querySelector("#formularioProducto");

        if (!formulario) return;

        formulario.reset();

        document.querySelector("#productoId").value = "";

        document.querySelector("#tituloFormulario").textContent =
            "Crear producto";

        document.querySelector("#botonGuardarProducto").textContent =
            "Crear producto";

        document.querySelector("#botonCancelarEdicion").hidden = true;

        document.querySelector("#activoProducto").checked = true;
    }

    // Eliminar producto.
    async eliminarProducto(id) {

        if (!confirm("¿Seguro que quieres eliminar este producto?")) {
            return;
        }

        try {

            const datos = await realizarPeticion(
                `${this.apiUrl}&funcion=eliminar`,
                "DELETE",
                { id: Number(id) }
            );

            alert(datos.mensaje || "Producto eliminado correctamente.");

            await this.obtenerProductos();

        } catch (error) {

            console.error("Error al eliminar producto:", error);
            alert(error.message);

        }
    }

}


/* =====================================================
   GESTIÓN DE USUARIOS
===================================================== */

class Usuarios {

    constructor() {
        this.usuarios = [];
        this.apiUrl = crearApiUrl("usuario");
    }

    inicializar() {

        const formulario = document.querySelector("#formularioUsuario");

        if (formulario) {

            formulario.addEventListener("submit", e => {
                this.guardarUsuario(e);
            });

        }

        const botonCancelar = document.querySelector("#botonCancelarUsuario");

        if (botonCancelar) {

            botonCancelar.addEventListener("click", () => {
                this.limpiarFormulario();
            });

        }

        this.obtenerUsuarios();
    }

    // Obtener usuarios.
    async obtenerUsuarios() {

        try {

            const datos = await realizarPeticion(
                `${this.apiUrl}&funcion=todos`
            );

            this.usuarios = datos.usuarios || [];

            this.mostrarUsuarios();

        } catch (error) {

            console.error("Error al cargar usuarios:", error);

            const tabla = document.querySelector("#tablaUsuarios");

            if (tabla) {
                tabla.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-danger">
                            No se pudieron cargar los usuarios.
                        </td>
                    </tr>
                `;
            }

            console.error(error.message);
        }
    }

    // Mostrar usuarios.
    mostrarUsuarios() {

        const tabla = document.querySelector("#tablaUsuarios");

        if (!tabla) return;

        tabla.innerHTML = "";

        if (this.usuarios.length === 0) {

            const fila = document.createElement("tr");
            const celda = crearCelda("No hay usuarios registrados.");

            celda.colSpan = 9;
            fila.appendChild(celda);
            tabla.appendChild(fila);

            return;
        }

        this.usuarios.forEach(usuario => {

            const fila = document.createElement("tr");

            const valores = [
                usuario.id,
                usuario.nombre,
                usuario.email,
                usuario.telefono,
                usuario.direccion,
                usuario.fecha_nacimiento,
                usuario.rol,
                usuario.fecha_registro
            ];

            valores.forEach(valor => {
                fila.appendChild(crearCelda(valor));
            });

            const acciones = document.createElement("td");

            // Editar.
            const botonEditar = document.createElement("button");

            botonEditar.type = "button";
            botonEditar.textContent = "Editar";
            botonEditar.className = "btn btn-warning btn-sm me-2";

            botonEditar.addEventListener("click", () => {
                this.editarUsuario(usuario.id);
            });

            // Eliminar.
            const botonEliminar = document.createElement("button");

            botonEliminar.type = "button";
            botonEliminar.textContent = "Eliminar";
            botonEliminar.className = "btn btn-danger btn-sm me-2";

            botonEliminar.addEventListener("click", () => {
                this.eliminarUsuario(usuario.id);
            });

            // Selector de rol.
            const selectorRol = document.createElement("select");

            selectorRol.className = "form-select form-select-sm mt-2";

            ["usuario", "admin"].forEach(rol => {

                const opcion = document.createElement("option");

                opcion.value = rol;
                opcion.textContent = rol;
                opcion.selected = usuario.rol === rol;

                selectorRol.appendChild(opcion);

            });

            // Cambiar rol.
            const botonRol = document.createElement("button");

            botonRol.type = "button";
            botonRol.textContent = "Cambiar rol";
            botonRol.className = "btn btn-primary btn-sm mt-2";

            botonRol.addEventListener("click", () => {
                this.cambiarRol(usuario.id, selectorRol.value);
            });

            acciones.append(
                botonEditar,
                botonEliminar,
                selectorRol,
                botonRol
            );

            fila.appendChild(acciones);
            tabla.appendChild(fila);

        });
    }

    // Guardar usuario.
    async guardarUsuario(e) {

        e.preventDefault();

        const formulario = document.querySelector("#formularioUsuario");

        const id = document.querySelector("#usuarioId").value;

        const password = document.querySelector("#passwordUsuario").value;

        const usuario = {
            nombre: document.querySelector("#nombreUsuario").value.trim(),
            email: document.querySelector("#emailUsuario").value.trim(),
            telefono: document.querySelector("#telefonoUsuario").value.trim(),
            direccion: document.querySelector("#direccionUsuario").value.trim(),
            fecha_nacimiento: document.querySelector("#fechaNacimientoUsuario").value,
            rol: document.querySelector("#rolUsuario").value
        };

        const editando = id !== "";

        if (
            !usuario.nombre ||
            !usuario.email ||
            !usuario.fecha_nacimiento
        ) {
            alert("Completa los campos obligatorios.");
            return;
        }

        if (!editando && !password) {
            alert("La contraseña es obligatoria al crear un usuario.");
            return;
        }

        if (!formulario.checkValidity()) {
            formulario.reportValidity();
            return;
        }

        if (password !== "") {
            usuario.password = password;
        }

        try {

            const datos = await realizarPeticion(
                `${this.apiUrl}&funcion=${editando ? "actualizar" : "crear"}`,
                editando ? "PUT" : "POST",
                editando
                    ? { ...usuario, id: Number(id) }
                    : usuario
            );

            alert(datos.mensaje || "Usuario guardado correctamente.");

            this.limpiarFormulario();

            await this.obtenerUsuarios();

        } catch (error) {

            console.error("Error al guardar usuario:", error);
            alert(error.message);

        }
    }

    // Editar usuario.
    editarUsuario(id) {

        const usuario = this.usuarios.find(
            u => Number(u.id) === Number(id)
        );

        if (!usuario) {
            alert("No se ha encontrado el usuario.");
            return;
        }

        document.querySelector("#usuarioId").value = usuario.id;

        document.querySelector("#nombreUsuario").value =
            usuario.nombre ?? "";

        document.querySelector("#emailUsuario").value =
            usuario.email ?? "";

        document.querySelector("#passwordUsuario").value = "";

        document.querySelector("#telefonoUsuario").value =
            usuario.telefono ?? "";

        document.querySelector("#direccionUsuario").value =
            usuario.direccion ?? "";

        document.querySelector("#fechaNacimientoUsuario").value =
            usuario.fecha_nacimiento ?? "";

        document.querySelector("#rolUsuario").value =
            usuario.rol ?? "usuario";

        document.querySelector("#tituloFormularioUsuario").textContent =
            "Editar usuario";

        document.querySelector("#botonGuardarUsuario").textContent =
            "Guardar cambios";

        document.querySelector("#botonCancelarUsuario").hidden = false;

        // Mostrar sección usuarios.
        const enlaceUsuarios = document.querySelector(
            '[data-menu="usuarios"]'
        );

        if (enlaceUsuarios) {
            enlaceUsuarios.click();
        }

        document.querySelector("#formularioUsuario").scrollIntoView({
            behavior: "smooth",
            block: "start"
        });
    }

    // Limpiar formulario.
    limpiarFormulario() {

        const formulario = document.querySelector("#formularioUsuario");

        if (!formulario) return;

        formulario.reset();

        document.querySelector("#usuarioId").value = "";

        document.querySelector("#tituloFormularioUsuario").textContent =
            "Crear usuario";

        document.querySelector("#botonGuardarUsuario").textContent =
            "Crear usuario";

        document.querySelector("#botonCancelarUsuario").hidden = true;

        document.querySelector("#rolUsuario").value = "usuario";
    }

    // Eliminar usuario.
    async eliminarUsuario(id) {

        if (!confirm("¿Seguro que quieres eliminar este usuario?")) {
            return;
        }

        try {

            const datos = await realizarPeticion(
                `${this.apiUrl}&funcion=eliminar`,
                "DELETE",
                { id: Number(id) }
            );

            alert(datos.mensaje || "Usuario eliminado correctamente.");

            await this.obtenerUsuarios();

        } catch (error) {

            console.error("Error al eliminar usuario:", error);
            alert(error.message);

        }
    }

    // Cambiar rol.
    async cambiarRol(id, rol) {

        if (!["usuario", "admin"].includes(rol)) {
            alert("El rol seleccionado no es válido.");
            return;
        }

        if (!confirm(`¿Quieres cambiar el rol de este usuario a ${rol}?`)) {
            return;
        }

        try {

            const datos = await realizarPeticion(
                `${this.apiUrl}&funcion=cambiarRol`,
                "PUT",
                {
                    id: Number(id),
                    rol: rol
                }
            );

            alert(datos.mensaje || "Rol actualizado correctamente.");

            await this.obtenerUsuarios();

        } catch (error) {

            console.error("Error al cambiar el rol:", error);
            alert(error.message);

        }
    }

}


/* =====================================================
   INICIALIZAR ADMINISTRADOR
===================================================== */

document.addEventListener("DOMContentLoaded", () => {

    try {

        const admin = new AdministrarVistas();

        admin.inicializar();

        console.log("Panel de administración inicializado correctamente.");

    } catch (error) {

        console.error("Error al inicializar el panel:", error);

    }

});
