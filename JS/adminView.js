
class administrarVistas {

    constructor() {
        this.enlaces = null;
        this.secciones = null;
        this.vista = "inicio";
        this.productos = [];
        this.categorias = null;
        this.platos = null;
        this.usuarios = null;

        this.apiUrl = "http://localhost/Proyecto_Lenaul/?controller=producto&action=api";
    }

    inicializar() {

        // Seleccionamos los enlaces y las secciones
        this.enlaces = document.querySelectorAll("a[data-view]");
        this.secciones = document.querySelectorAll("section[data-view]");

        // Eventos para cambiar de vista
        this.enlaces.forEach(enlace => {

            enlace.addEventListener("click", (e) => {

                e.preventDefault();

                this.vista = enlace.dataset.view;

                this.mostrarVista();

            });

        });

        // Mostramos la vista inicial
        this.mostrarVista();

        // Cargamos los productos
        this.obtenerProductos();
    }


    // Mostrar y ocultar secciones
    mostrarVista() {

        this.secciones.forEach(seccion => {

            if (seccion.dataset.view === this.vista) {
                seccion.hidden = false;
            } else {
                seccion.hidden = true;
            }

        });

    }


    // Obtener productos desde la API
    obtenerProductos() {

        fetch(`${this.apiUrl}&funcion=todos`)

            .then(respuesta => {

                if (!respuesta.ok) {
                    throw new Error("Error al conectar con la API");
                }

                return respuesta.json();

            })

            .then(datos => {

                if (!datos.success) {
                    throw new Error(datos.mensaje || "No se pudieron cargar los productos");
                }

                this.productos = datos.productos;

                this.mostrarProductos();

            })

            .catch(error => {

                console.error("Error:", error);

            });

    }


    // Mostrar productos en la tabla
    mostrarProductos() {

        const tablaProductos = document.querySelector("#tablaProductos");

        if (!tablaProductos) return;

        tablaProductos.innerHTML = "";

        this.productos.forEach(p => {

            const tr = document.createElement("tr");

            const valores = [
                p.id,
                p.categoria_id,
                p.nombre,
                p.descripcion,
                p.precio,
                p.imagen,
                p.activo ? "Sí" : "No",
                p.tipo
            ];

            valores.forEach(valor => {

                const td = document.createElement("td");

                td.textContent = valor ?? "";

                tr.appendChild(td);

            });

            tablaProductos.appendChild(tr);

        });

    }


    // Eliminar producto
    eliminarProducto(e) {

        e.preventDefault();

        const id = Number(document.querySelector("#productoId").value);

        if (!Number.isInteger(id) || id <= 0) {
            alert("Introduce un ID válido");
            return;
        }

        if (!confirm("¿Seguro que quieres eliminar este producto?")) {
            return;
        }

        fetch(`${this.apiUrl}&funcion=eliminar`, {

            method: "DELETE",

            headers: {
                "Content-Type": "application/json"
            },

            body: JSON.stringify({ id: id })

        })

        .then(respuesta => respuesta.json())

        .then(datos => {

            if (!datos.success) {
                throw new Error(datos.mensaje || "No se pudo eliminar el producto");
            }

            alert(datos.mensaje);

            document.querySelector("#productoId").value = "";

            this.obtenerProductos();

        })

        .catch(error => {

            console.error(error);
            alert(error.message);

        });

    }

}


// Crear objeto e inicializar
const admin = new administrarVistas();

admin.inicializar();


// Formulario de eliminacion
const formularioEliminarProducto = document.querySelector("#formularioEliminarProducto");

if (formularioEliminarProducto) {

    formularioEliminarProducto.addEventListener("submit", (e) => {

        admin.eliminarProducto(e);

    });

}
