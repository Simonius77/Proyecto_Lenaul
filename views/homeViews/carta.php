<?php
// Convertimos los productos y sus ingredientes a JSON para JavaScript.
$productosJS = [];

foreach ($listaproductos as $producto) {
    if (!$producto->getActivo()) {
        continue;
    }

    $ingredientes = IngredienteDAO::getByProductoId($producto->getId());

    $productosJS[] = [
        'id' => $producto->getId(),
        'nombre' => $producto->getNombre(),
        'descripcion' => $producto->getDescripcion(),
        'precio' => (float) $producto->getPrecio(),
        'imagen' => $producto->getImagen(),
        'tipo' => $producto->getTipo(),
        'ingredientes' => array_map(function ($ingrediente) {
            return [
                'id' => (int) $ingrediente['id'],
                'nombre' => $ingrediente['nombre'],
                'precio_extra' => (float) $ingrediente['precio_extra'],
                'cantidad' => (float) $ingrediente['cantidad']
            ];
        }, $ingredientes)
    ];
}

$ingredientesDisponibles = IngredienteDAO::getAll();
?>

<div class="container py-5">

    <div class="text-center mb-5">
        <h1 class="fw-bold font-monospace text-dark mb-2">
            NUESTRA CARTA
        </h1>

        <div style="width:80px;height:4px;background:#ffc107;margin:0 auto;"
             class="rounded-pill"></div>

        <p class="text-muted mt-3 fs-5">
            Sabores autenticos, ingredientes frescos y recetas tradicionales elaboradas con amor.
        </p>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4">
            <i class="fas fa-check-circle me-2"></i>
            <?= htmlspecialchars($_SESSION['success']); ?>
            <?php unset($_SESSION['success']); ?>

            <button type="button" class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- CARTA -->
    <div id="cartaProductos"
         class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
    </div>

    <!-- RESUMEN DEL PEDIDO -->
    <div class="card shadow border-0 rounded-4 mt-5">
        <div class="card-body p-4">

            <h3 class="fw-bold mb-4">
                <i class="fas fa-shopping-cart text-warning me-2"></i>
                Tu pedido
            </h3>

            <div id="resumenPedido">
                <p class="text-muted">
                    Todavía no has añadido productos.
                </p>
            </div>

            <hr>

            <div class="d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0">TOTAL</h4>

                <h3 class="fw-bold text-success mb-0"
                    id="totalPedido">0,00 €</h3>
            </div>

            <button type="button"
                    class="btn btn-warning btn-lg fw-bold w-100 mt-4"
                    id="btnConfirmar"
                    disabled
                    onclick="confirmarPedido()">
                Continuar con el pedido
            </button>

        </div>
    </div>

</div>

<style>
    .producto-card {
        transition: transform .3s, box-shadow .3s;
    }

    .producto-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,.1) !important;
    }

    .producto-imagen {
        height: 200px;
        object-fit: cover;
        transition: transform .5s;
    }

    .producto-card:hover .producto-imagen {
        transform: scale(1.05);
    }

    .btn-warning {
        background: #ffc107;
        border: none;
    }

    .btn-warning:hover {
        background: #e5ac00;
    }

    .ingrediente-fila {
        border-bottom: 1px solid #eee;
        padding: 9px 0;
    }

    .cantidad-control {
        width: 42px;
        height: 35px;
        padding: 0;
    }

    .cantidad-ingrediente {
        width: 65px;
    }

    .precio-extra {
        font-size: .8rem;
        color: #198754;
    }

    .resumen-linea {
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }
</style>

<script>
const productos = <?= json_encode(
    $productosJS,
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
); ?>;

const ingredientesDisponibles = <?= json_encode(
    array_map(function ($i) {
        return [
            'id' => (int) $i['id'],
            'nombre' => $i['nombre'],
            'precio_extra' => (float) $i['precio_extra']
        ];
    }, $ingredientesDisponibles),
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
); ?>;

// Aquí guardamos los productos seleccionados.
let pedido = {};

// Formato de precio.
function euros(precio) {
    return Number(precio).toFixed(2).replace('.', ',') + ' €';
}

// Escapamos texto procedente de la base de datos.
function escapar(texto) {
    return String(texto ?? '').replace(/[&<>"']/g, caracter => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
    })[caracter]);
}

// Inicializar ingredientes de un producto.
function inicializarProducto(producto) {
    const ingredientes = {};

    producto.ingredientes.forEach(i => {
        ingredientes[i.id] = {
            id: i.id,
            nombre: i.nombre,
            incluido: Number(i.cantidad),
            cantidad: Number(i.cantidad),
            precio_extra: Number(i.precio_extra)
        };
    });

    return {
        id: producto.id,
        nombre: producto.nombre,
        precio: Number(producto.precio),
        cantidad: 0,
        ingredientes: ingredientes,
        extras: {}
    };
}

// Obtener suplemento de ingredientes de una ración.
function calcularExtras(item) {
    let total = 0;

    Object.values(item.ingredientes).forEach(i => {
        const adicionales = Math.max(0, i.cantidad - i.incluido);
        total += adicionales * i.precio_extra;
    });

    Object.values(item.extras).forEach(i => {
        total += i.cantidad * i.precio_extra;
    });

    return total;
}

// Precio de una unidad personalizada.
function precioUnitario(item) {
    return item.precio + calcularExtras(item);
}

// Total de una línea.
function subtotal(item) {
    return precioUnitario(item) * item.cantidad;
}

// Mostrar todos los productos.
function mostrarCarta() {
    const contenedor = document.getElementById('cartaProductos');

    contenedor.innerHTML = productos.map(producto => {
        const imagen = producto.imagen
            ? escapar(producto.imagen).replace(/^\/+/, '')
            : 'Imagenes/default.png';

        return `
            <div class="col d-flex">
                <div class="card producto-card w-100 shadow-sm border-0 rounded-4 overflow-hidden bg-white">

                    <div class="position-relative overflow-hidden"
                         style="height:200px;">

                        <img src="${imagen}"
                             class="w-100 h-100 producto-imagen"
                             onerror="this.onerror=null;this.src='Imagenes/default.png'">

                        <div class="position-absolute bottom-0 start-0 bg-dark text-warning fw-bold px-3 py-2"
                             style="background:rgba(0,0,0,.75)!important;">
                            ${euros(producto.precio)}
                        </div>
                    </div>

                    <div class="card-body p-4 d-flex flex-column">

                        <h5 class="fw-bold text-dark">
                            ${escapar(producto.nombre)}
                        </h5>

                        <p class="text-muted flex-grow-1">
                            ${escapar(producto.descripcion)}
                        </p>

                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="fw-bold">Cantidad</span>

                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-outline-warning cantidad-control"
                                        onclick="cambiarCantidad(${producto.id}, -1)">
                                    −
                                </button>

                                <span class="fw-bold"
                                      id="cantidad-${producto.id}">0</span>

                                <button class="btn btn-outline-warning cantidad-control"
                                        onclick="cambiarCantidad(${producto.id}, 1)">
                                    +
                                </button>
                            </div>
                        </div>

                        <button class="btn btn-warning fw-bold w-100"
                                onclick="agregarProducto(${producto.id})">
                            <i class="fas fa-plus-circle me-2"></i>
                            Personalizar producto
                        </button>

                        <div id="personalizar-${producto.id}"
                             class="mt-3"></div>

                    </div>
                </div>
            </div>
        `;
    }).join('');
}

// Cambiar cantidad del producto.
function cambiarCantidad(id, cambio) {
    const producto = productos.find(p => p.id === id);

    if (!pedido[id]) {
        pedido[id] = inicializarProducto(producto);
    }

    pedido[id].cantidad = Math.max(
        0,
        pedido[id].cantidad + cambio
    );

    document.getElementById('cantidad-' + id).textContent =
        pedido[id].cantidad;

    if (pedido[id].cantidad === 0) {
        delete pedido[id];
        document.getElementById('personalizar-' + id).innerHTML = '';
    }

    actualizarResumen();
}

// Añadir un producto al pedido.
function agregarProducto(id) {
    const producto = productos.find(p => p.id === id);

    if (!pedido[id]) {
        pedido[id] = inicializarProducto(producto);
    }

    if (pedido[id].cantidad === 0) {
        pedido[id].cantidad = 1;
        document.getElementById('cantidad-' + id).textContent = 1;
    }

    mostrarPersonalizacion(id);
    actualizarResumen();
}

// Mostrar ingredientes personalizables.
function mostrarPersonalizacion(id) {
    const item = pedido[id];

    if (!item) return;

    const ingredientesHTML = Object.values(item.ingredientes).map(i => `
        <div class="ingrediente-fila">

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="fw-semibold">${escapar(i.nombre)}</span>
                    <div class="precio-extra">
                        ${euros(i.precio_extra)} por unidad adicional
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-outline-secondary"
                            onclick="cambiarIngrediente(${id}, ${i.id}, -1)">
                        −
                    </button>

                    <input type="number"
                           class="form-control form-control-sm cantidad-ingrediente"
                           min="0"
                           step="1"
                           value="${i.cantidad}"
                           onchange="ponerCantidadIngrediente(${id}, ${i.id}, this.value)">

                    <button class="btn btn-sm btn-outline-secondary"
                            onclick="cambiarIngrediente(${id}, ${i.id}, 1)">
                        +
                    </button>
                </div>
            </div>

            <small class="text-muted">
                Cantidad original: ${i.incluido}.
                Pon 0 para quitarlo.
            </small>

        </div>
    `).join('');

    // Ingredientes que todavía no están en el producto.
    const opcionesExtras = ingredientesDisponibles
        .filter(i =>
            !item.ingredientes[i.id] &&
            !item.extras[i.id]
        )
        .map(i => `
            <option value="${i.id}">
                ${escapar(i.nombre)} (+${euros(i.precio_extra)})
            </option>
        `).join('');

    document.getElementById('personalizar-' + id).innerHTML = `
        <div class="border rounded-3 p-3 bg-light">

            <h6 class="fw-bold mb-3">
                <i class="fas fa-sliders-h text-warning me-2"></i>
                Personaliza tu producto
            </h6>

            ${ingredientesHTML || '<p class="text-muted">Sin ingredientes asociados.</p>'}

            <hr>

            <label class="form-label fw-bold">
                Añadir ingrediente extra
            </label>

            <div class="input-group">
                <select class="form-select"
                        id="extra-${id}">
                    <option value="">Selecciona un ingrediente</option>
                    ${opcionesExtras}
                </select>

                <button class="btn btn-warning"
                        onclick="anadirExtra(${id})">
                    Añadir
                </button>
            </div>

            <div id="extras-anadidos-${id}" class="mt-2"></div>

            <hr>

            <div class="d-flex justify-content-between">
                <span>Precio por unidad:</span>
                <strong id="precio-unidad-${id}">
                    ${euros(precioUnitario(item))}
                </strong>
            </div>

        </div>
    `;

    mostrarExtrasAnadidos(id);
}

// Cambiar cantidad de un ingrediente incluido.
function cambiarIngrediente(productoId, ingredienteId, cambio) {
    const ingrediente = pedido[productoId].ingredientes[ingredienteId];

    ingrediente.cantidad = Math.max(
        0,
        ingrediente.cantidad + cambio
    );

    mostrarPersonalizacion(productoId);
    actualizarResumen();
}

// Escribir directamente la cantidad.
function ponerCantidadIngrediente(productoId, ingredienteId, valor) {
    let cantidad = parseInt(valor, 10);

    if (isNaN(cantidad) || cantidad < 0) {
        cantidad = 0;
    }

    pedido[productoId].ingredientes[ingredienteId].cantidad = cantidad;

    mostrarPersonalizacion(productoId);
    actualizarResumen();
}

// Añadir ingrediente extra.
function anadirExtra(productoId) {
    const selector = document.getElementById('extra-' + productoId);
    const ingredienteId = Number(selector.value);

    if (!ingredienteId) return;

    const ingrediente = ingredientesDisponibles.find(
        i => i.id === ingredienteId
    );

    if (!ingrediente) return;

    pedido[productoId].extras[ingredienteId] = {
        id: ingrediente.id,
        nombre: ingrediente.nombre,
        cantidad: 1,
        precio_extra: Number(ingrediente.precio_extra)
    };

    mostrarPersonalizacion(productoId);
    actualizarResumen();
}

// Mostrar extras añadidos.
function mostrarExtrasAnadidos(productoId) {
    const extras = pedido[productoId].extras;

    const html = Object.values(extras).map(i => `
        <div class="d-flex align-items-center justify-content-between py-2 border-bottom">

            <div>
                <strong>${escapar(i.nombre)}</strong>
                <div class="precio-extra">
                    ${euros(i.precio_extra)} por unidad
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-outline-secondary"
                        onclick="cambiarExtra(${productoId}, ${i.id}, -1)">
                    −
                </button>

                <span>${i.cantidad}</span>

                <button class="btn btn-sm btn-outline-secondary"
                        onclick="cambiarExtra(${productoId}, ${i.id}, 1)">
                    +
                </button>

                <button class="btn btn-sm btn-outline-danger"
                        onclick="eliminarExtra(${productoId}, ${i.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>

        </div>
    `).join('');

    document.getElementById('extras-anadidos-' + productoId).innerHTML = html;
}

// Modificar cantidad de un extra.
function cambiarExtra(productoId, ingredienteId, cambio) {
    const extra = pedido[productoId].extras[ingredienteId];

    extra.cantidad = Math.max(0, extra.cantidad + cambio);

    if (extra.cantidad === 0) {
        delete pedido[productoId].extras[ingredienteId];
    }

    mostrarPersonalizacion(productoId);
    actualizarResumen();
}

// Eliminar un extra.
function eliminarExtra(productoId, ingredienteId) {
    delete pedido[productoId].extras[ingredienteId];

    mostrarPersonalizacion(productoId);
    actualizarResumen();
}

// Actualizar el resumen y el total.
function actualizarResumen() {
    const resumen = document.getElementById('resumenPedido');
    const totalElemento = document.getElementById('totalPedido');

    let total = 0;
    let html = '';

    Object.values(pedido).forEach(item => {
        if (item.cantidad <= 0) return;

        const importe = subtotal(item);
        total += importe;

        html += `
            <div class="resumen-linea">
                <div class="d-flex justify-content-between">
                    <strong>${escapar(item.nombre)} x ${item.cantidad}</strong>
                    <strong>${euros(importe)}</strong>
                </div>

                <small class="text-muted">
                    ${euros(precioUnitario(item))} por unidad
                </small>
            </div>
        `;
    });

    resumen.innerHTML = html || `
        <p class="text-muted">Todavía no has añadido productos.</p>
    `;

    totalElemento.textContent = euros(total);

    document.getElementById('btnConfirmar').disabled =
        Object.keys(pedido).length === 0;
}

// Por ahora solo mostramos los datos del pedido.
function confirmarPedido() {
    console.log('Pedido preparado:', pedido);

    alert(
        'Tu pedido está preparado. El siguiente paso será conectarlo con la base de datos.'
    );
}

// Cargar la carta al abrir la página.
mostrarCarta();
</script>
</div>
