<?php

namespace App\config\Menu\Http\Routes;

use App\config\Menu\Http\Controller\MenuController;

class MenuRoutes
{
    public static function Routes($router)
    {
        $ctr = new MenuController();

        // Rutas
        $router->get('/menu', $ctr->Listar());
    }
}
