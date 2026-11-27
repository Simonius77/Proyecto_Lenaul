
<?php

class Pedido
{
    private ?int $id = null;
    private int $usuario_id = 0;
    private string $estado = 'pendiente';
    private ?string $fecha_pedido = null;
    private float $subtotal = 0.00;
    private float $descuento = 0.00;
    private float $iva = 0.00;
    private float $total = 0.00;
    private string $metodo_pago = 'simulado';
    private string $estado_pago = 'pendiente';

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUsuario_id(): int
    {
        return $this->usuario_id;
    }

    public function setUsuario_id(int $usuario_id): void
    {
        $this->usuario_id = $usuario_id;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): void
    {
        $this->estado = $estado;
    }

    public function getFecha_pedido(): ?string
    {
        return $this->fecha_pedido;
    }

    public function setFecha_pedido(?string $fecha_pedido): void
    {
        $this->fecha_pedido = $fecha_pedido;
    }

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function setSubtotal(float $subtotal): void
    {
        $this->subtotal = $subtotal;
    }

    public function getDescuento(): float
    {
        return $this->descuento;
    }

    public function setDescuento(float $descuento): void
    {
        $this->descuento = $descuento;
    }

    public function getIva(): float
    {
        return $this->iva;
    }

    public function setIva(float $iva): void
    {
        $this->iva = $iva;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float $total): void
    {
        $this->total = $total;
    }

    public function getMetodo_pago(): string
    {
        return $this->metodo_pago;
    }

    public function setMetodo_pago(string $metodo_pago): void
    {
        $this->metodo_pago = $metodo_pago;
    }

    public function getEstado_pago(): string
    {
        return $this->estado_pago;
    }

    public function setEstado_pago(string $estado_pago): void
    {
        $this->estado_pago = $estado_pago;
    }
}
