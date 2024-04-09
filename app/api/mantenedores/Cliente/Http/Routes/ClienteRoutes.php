<?php
namespace Mnt\mantenedores\Cliente\Http\Routes;

use Mnt\mantenedores\Cliente\Http\Controller\ClienteController;

class ClienteRoutes
{
    public static function Routes($router)
    {
        $ctr = new ClienteController();
    
        // Rutas
        $router->get('/cliente-buscar', $ctr->Buscar());
        $router->get('/cliente', $ctr->Listar());
        $router->post('/cliente', $ctr->Crear());
        $router->get('/cliente/[i:id]', $ctr->BuscarPorId());
        $router->put('/cliente/[i:id]', $ctr->Actualizar());
        $router->delete('/cliente/[i:id]', $ctr->Eliminar());
        $router->patch('/cliente/[i:id]/habilitar', $ctr->Habilitar());
        $router->patch('/cliente/[i:id]/deshabilitar', $ctr->Deshabilitar());
        $router->get('/cliente-codigo', $ctr->Codigo());
    }
}
