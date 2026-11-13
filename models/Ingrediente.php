
<?php

class Ingrediente
{
    private int $id;
    private string $nombre;
    private float $precio_extra;
    private bool $activo;
    private float $cantidad;

    public function __construct(
        int $id,
        string $nombre,
        float $precio_extra,
        bool $activo = true,
        float $cantidad = 1
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->precio_extra = $precio_extra;
        $this->activo = $activo;
        $this->cantidad = $cantidad;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getPrecioExtra(): float
    {
        return $this->precio_extra;
    }

    public function getActivo(): bool
    {
        return $this->activo;
    }

    public function getCantidad(): float
    {
        return $this->cantidad;
    }
}
