-- lenaul — script de inicialización
-- Uso (MySQL/MariaDB):
--   mysql -u root < sql/init.sql
-- O desde phpMyAdmin: Importar este archivo.

CREATE DATABASE IF NOT EXISTS lenaul
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE lenaul;

-- =========================================
-- 1. TABLA: usuarios
-- =========================================

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    direccion VARCHAR(255),
    fecha_nacimiento DATE,
    rol VARCHAR(20) NOT NULL DEFAULT 'usuario',
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT chk_usuarios_rol
        CHECK (rol IN ('usuario', 'admin'))
) ENGINE=InnoDB;

-- =========================================
-- 2. TABLA: categorias
-- =========================================

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    activa BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB;

-- =========================================
-- 3. TABLA: productos
-- =========================================

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    imagen VARCHAR(255),
    activo BOOLEAN NOT NULL DEFAULT TRUE,

    CONSTRAINT chk_productos_precio
        CHECK (precio >= 0),

    CONSTRAINT fk_productos_categoria
        FOREIGN KEY (categoria_id)
        REFERENCES categorias(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- =========================================
-- 4. TABLA: ingredientes
-- =========================================

CREATE TABLE ingredientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    precio_extra DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    activo BOOLEAN NOT NULL DEFAULT TRUE,

    CONSTRAINT chk_ingredientes_precio
        CHECK (precio_extra >= 0)
) ENGINE=InnoDB;

-- =========================================
-- 5. TABLA: producto_ingrediente
-- =========================================

CREATE TABLE producto_ingrediente (
    producto_id INT NOT NULL,
    ingrediente_id INT NOT NULL,
    cantidad DECIMAL(10,2) NOT NULL DEFAULT 1,

    PRIMARY KEY (producto_id, ingrediente_id),

    CONSTRAINT chk_producto_ingrediente_cantidad
        CHECK (cantidad > 0),

    CONSTRAINT fk_pi_producto
        FOREIGN KEY (producto_id)
        REFERENCES productos(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_pi_ingrediente
        FOREIGN KEY (ingrediente_id)
        REFERENCES ingredientes(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================
-- 6. TABLA: ofertas
-- =========================================

CREATE TABLE ofertas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    tipo VARCHAR(20) NOT NULL,
    valor DECIMAL(10,2),
    codigo VARCHAR(50) UNIQUE,
    fecha_inicio DATETIME NOT NULL,
    fecha_fin DATETIME,
    activa BOOLEAN NOT NULL DEFAULT TRUE,

    CONSTRAINT chk_ofertas_tipo
        CHECK (tipo IN ('descuento', '2x1', 'combo', 'codigo')),

    CONSTRAINT chk_ofertas_fechas
        CHECK (fecha_fin IS NULL OR fecha_fin > fecha_inicio),

    CONSTRAINT chk_ofertas_valor
        CHECK (valor IS NULL OR valor >= 0)
) ENGINE=InnoDB;

-- =========================================
-- 7. TABLA: oferta_producto
-- =========================================

CREATE TABLE oferta_producto (
    oferta_id INT NOT NULL,
    producto_id INT NOT NULL,

    PRIMARY KEY (oferta_id, producto_id),

    CONSTRAINT fk_op_oferta
        FOREIGN KEY (oferta_id)
        REFERENCES ofertas(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_op_producto
        FOREIGN KEY (producto_id)
        REFERENCES productos(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================
-- 8. TABLA: pedidos
-- =========================================

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    fecha_pedido DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    subtotal DECIMAL(10,2) NOT NULL,
    descuento DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    metodo_pago VARCHAR(50) NOT NULL DEFAULT 'simulado',
    estado_pago VARCHAR(20) NOT NULL DEFAULT 'pendiente',

    CONSTRAINT chk_pedidos_estado
        CHECK (estado IN (
            'pendiente', 'confirmado', 'preparando',
            'enviado', 'entregado', 'cancelado'
        )),

    CONSTRAINT chk_pedidos_estado_pago
        CHECK (estado_pago IN ('pendiente', 'pagado', 'fallido')),

    CONSTRAINT chk_pedidos_importes
        CHECK (
            subtotal >= 0
            AND descuento >= 0
            AND total >= 0
        ),

    CONSTRAINT fk_pedidos_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- =========================================
-- 9. TABLA: pedido_detalle
-- =========================================

CREATE TABLE pedido_detalle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    producto_id INT,
    nombre_producto VARCHAR(150) NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    cantidad INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,

    CONSTRAINT chk_pedido_detalle_importes
        CHECK (
            cantidad > 0
            AND precio_unitario >= 0
            AND subtotal >= 0
        ),

    CONSTRAINT fk_pd_pedido
        FOREIGN KEY (pedido_id)
        REFERENCES pedidos(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_pd_producto
        FOREIGN KEY (producto_id)
        REFERENCES productos(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- =========================================
-- 10. TABLA: pedido_detalle_ingrediente
-- =========================================

CREATE TABLE pedido_detalle_ingrediente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_detalle_id INT NOT NULL,
    ingrediente_id INT,
    nombre_ingrediente VARCHAR(100) NOT NULL,
    precio_extra DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    accion VARCHAR(20) NOT NULL,

    CONSTRAINT chk_pdi_precio
        CHECK (precio_extra >= 0),

    CONSTRAINT chk_pdi_accion
        CHECK (accion IN ('incluido', 'quitado', 'añadido')),

    CONSTRAINT fk_pdi_detalle
        FOREIGN KEY (pedido_detalle_id)
        REFERENCES pedido_detalle(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_pdi_ingrediente
        FOREIGN KEY (ingrediente_id)
        REFERENCES ingredientes(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB;


-- CREA LA TABLA USUARIOS SI NO EXISTIERA

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

--FIN

