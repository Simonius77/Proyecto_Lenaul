
<?php

include_once __DIR__ . '/../models/ProductoDAO.php';

class homeController
{
    public function index()
    {
        $this->Home();
    }

    public function Home()
    {
        $listaproductos = ProductoDAO::getAll();

        $view = "views/homeViews/home.php";
        require __DIR__ . '/../views/main.php';
    }

    public function Legal()
    {
        $view = "views/homeViews/legal.php";
        require __DIR__ . '/../views/main.php';
    }

    public function Politicas()
    {
        $view = "views/homeViews/politica_priv.php";
        require __DIR__ . '/../views/main.php';
    }

    public function AboutUs()
    {
        $view = "views/homeViews/quienes_somos.php";
        require __DIR__ . '/../views/main.php';
    }

    public function Login()
    {
        $view = "views/admindViews/login.php";
        require __DIR__ . '/../views/main.php';
    }
}






















































    