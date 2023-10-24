<?php
namespace Mnt\mantenedores\Empleado\Http\Routes;

use Mnt\mantenedores\Empleado\Http\Controller\EmpleadoController;

class EmpleadoRoutes
{
    public static function Routes($router)
    {
        $ctr = new EmpleadoController();
    
        // Rutas
        $router->get('/empleado', $ctr->Listar());
        $router->post('/empleado', $ctr->Crear());
        $router->get('/empleado/[i:id]', $ctr->BuscarPorId());
        $router->put('/empleado/[i:id]', $ctr->Actualizar());
        $router->delete('/empleado/[i:id]', $ctr->Eliminar());
        $router->patch('/empleado/[i:id]/habilitar', $ctr->Habilitar());
        $router->patch('/empleado/[i:id]/deshabilitar', $ctr->Deshabilitar());
        
    }
}
