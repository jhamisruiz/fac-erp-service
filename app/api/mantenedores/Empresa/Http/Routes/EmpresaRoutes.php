<?php

namespace Mnt\mantenedores\Empresa\Http\Routes;

use Mnt\mantenedores\Empresa\Http\Controller\EmpresaController;

class EmpresaRoutes
{
    public static function Routes($router)
    {
        $ctr = new EmpresaController();

        // Rutas
        $router->get('/empresa-buscar', $ctr->Buscar());
        $router->get('/empresa', $ctr->Listar());
        $router->post('/empresa', $ctr->Crear());
        $router->get('/empresa/[i:id]', $ctr->BuscarPorId());
        $router->put('/empresa/[i:id]', $ctr->Actualizar());
        $router->delete('/empresa/[i:id]', $ctr->Eliminar());
        $router->patch('/empresa/[i:id]/habilitar', $ctr->Habilitar());
        $router->patch('/empresa/[i:id]/deshabilitar', $ctr->Deshabilitar());
        $router->get('/empresa-codigo', $ctr->Codigo());
    }
}
