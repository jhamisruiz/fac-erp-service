<?php

namespace App\config\Menu\Http\Routes;

use App\config\Menu\Http\Controller\MenuController;

class MenuRoutes
{
    public static function Routes($router)
    {
        $ctr = new MenuController();

        // Rutas
        $router->get('/menu-buscar', $ctr->Buscar());
        $router->get('/menu', $ctr->Listar());
        $router->post('/menu', $ctr->Crear());
        $router->get('/menu/[i:id]', $ctr->BuscarPorId());
        $router->put('/menu/[i:id]', $ctr->Actualizar());
        $router->delete('/menu/[i:id]', $ctr->Eliminar());
        $router->patch('/menu/[i:id]/habilitar', $ctr->Habilitar());
        $router->patch('/menu/[i:id]/deshabilitar', $ctr->Deshabilitar());
        $router->get('/menu-codigo', $ctr->Codigo());
    }
}
