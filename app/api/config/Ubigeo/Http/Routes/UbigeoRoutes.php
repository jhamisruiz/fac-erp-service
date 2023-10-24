<?php

namespace App\config\Ubigeo\Http\Routes;

use App\config\Ubigeo\Http\Controller\UbigeoController;

class UbigeoRoutes
{
    public static function Routes($router)
    {
        $ctr = new UbigeoController();

        // Rutas
        $router->get('/ubigeo-departamento', $ctr->Departamento());
        $router->get('/ubigeo-provincia/[i:provincia]', $ctr->Provincia());
        $router->get('/ubigeo-distrito/[i:distrito]', $ctr->Distrito());
    }
}
