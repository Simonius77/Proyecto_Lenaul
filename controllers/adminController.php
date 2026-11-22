
<?php
//controller para la vista del panel de admin MasterClass del 18/11/26 David Navarro
class adminController {
    //Funcion para mostrar la vista del panel de admin
    public function index()
    {
        //a la variable view le asignamos la ruta de la vista del panel de admin
        $view = "views/admin/admin.php";
        //requerimos la vista principal
        require __DIR__ . '/../views/main.php';
    }

}