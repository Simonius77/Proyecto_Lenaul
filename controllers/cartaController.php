<?php

include_once __DIR__ . '/../models/ProductoDAO.php';
include_once __DIR__ . '/../models/IngredienteDAO.php';

class cartaController
{
    public function index()
    {
        $listaproductos = ProductoDAO::getAll();

        $listaproductos = array_filter(
            $listaproductos,
            fn($producto) => $producto->getActivo()
        );

        $listaproductos = array_values($listaproductos);

        $ingredientesDisponibles = IngredienteDAO::getAll();

        $view = __DIR__ . '/../views/homeViews/carta.php';
        require __DIR__ . '/../views/main.php';
    }
}