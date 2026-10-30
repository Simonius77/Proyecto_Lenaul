
<?php

abstract class Producto
{
    protected ?int $id;
    protected int $categoria_id;
    protected string $nombre;
    protected ?string $descripcion;
    protected float $precio;
    protected ?string $imagen;
    protected bool $activo;

    public function __construct(
        int $categoria_id,
        string $nombre,
        ?string $descripcion,
        float $precio,
        ?string $imagen = null,
        bool $activo = true,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->categoria_id = $categoria_id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->precio = $precio;
        $this->imagen = $imagen;
        $this->activo = $activo;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoriaId(): int
    {
        return $this->categoria_id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function getPrecio(): float
    {
        return $this->precio;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function getActivo(): bool
    {
        return $this->activo;
    }

    // Cada clase hija tendrá que definir su tipo.
    abstract public function getTipo(): string;
}
