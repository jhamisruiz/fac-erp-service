<?php

namespace Mnt\mantenedores\Sucursal\Http\Routes;

use Mnt\mantenedores\Sucursal\Http\Controller\SucursalController;

class SucursalRoutes
{
    public static function Routes($router)
    {
        $ctr = new SucursalController();

        // Rutas
        $router->get('/sucursal-buscar', $ctr->Buscar());
        $router->get('/sucursal', $ctr->Listar());
        $router->post('/sucursal', $ctr->Crear());
        $router->get('/sucursal/[i:id]', $ctr->BuscarPorId());
        $router->put('/sucursal/[i:id]', $ctr->Actualizar());
        $router->delete('/sucursal/[i:id]', $ctr->Eliminar());
        $router->patch('/sucursal/[i:id]/habilitar', $ctr->Habilitar());
        $router->patch('/sucursal/[i:id]/deshabilitar', $ctr->Deshabilitar());
        $router->get('/sucursal-codigo', $ctr->Codigo());

        $router->get('/sucursal-empresa', $ctr->SucursalEmpresa());
    }
}
