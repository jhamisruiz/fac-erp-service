<?php

namespace App\Utils\Auth\Http\Routes;

use App\Utils\Auth\Http\Controller\AuthController;

class AuthRoutes
{
    public static function Routes($router)
    {
        $ctr = new AuthController();

        // Rutas
        $router->post('/login', $ctr->Login());
        $router->post('/logout', $ctr->Logout());
        $router->post('/user-ruc-dni', $ctr->RucDni());
    }
}
