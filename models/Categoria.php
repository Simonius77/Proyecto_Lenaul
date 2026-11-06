
<?php

class Categoria
{
    private ?int $id;
    private string $nombre;
    private ?string $descripcion;
    private bool $activo;

    public function __construct(
        string $nombre,
        ?string $descripcion = null,
        bool $activo = true,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->activo = $activo;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): void
    {
        $this->descripcion = $descripcion;
    }

    public function getActivo(): bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): void
    {
        $this->activo = $activo;
    }
}
