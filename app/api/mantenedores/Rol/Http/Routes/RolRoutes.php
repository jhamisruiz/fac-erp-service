<?php

namespace Mnt\mantenedores\Rol\Http\Routes;

use Mnt\mantenedores\Rol\Http\Controller\RolController;

class RolRoutes
{
    public static function Routes($router)
    {
        $ctr = new RolController();

        // Rutas
        $router->get('/rol-buscar', $ctr->Buscar());
        $router->get('/rol', $ctr->Listar());
        $router->post('/rol', $ctr->Crear());
        $router->get('/rol/[i:id]', $ctr->BuscarPorId());
        $router->get('/rol-detalle/[i:id]', $ctr->BuscarDetallePorId());
        $router->put('/rol/[i:id]', $ctr->Actualizar());
        $router->delete('/rol/[i:id]', $ctr->Eliminar());
        $router->patch('/rol/[i:id]/habilitar', $ctr->Habilitar());
        $router->patch('/rol/[i:id]/deshabilitar', $ctr->Deshabilitar());
        $router->get('/rol-codigo', $ctr->Codigo());
    }
}
