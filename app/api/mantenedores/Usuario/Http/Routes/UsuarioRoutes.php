<?php
namespace Mnt\mantenedores\Usuario\Http\Routes;

use Mnt\mantenedores\Usuario\Http\Controller\UsuarioController;

class UsuarioRoutes
{
    public static function Routes($router)
    {
        $ctr = new UsuarioController();
    
        // Rutas
        $router->get('/usuario', $ctr->Listar());
        $router->post('/usuario', $ctr->Crear());
        $router->get('/usuario/[i:id]', $ctr->BuscarPorId());
        $router->put('/usuario/[i:id]', $ctr->Actualizar());
        $router->delete('/usuario/[i:id]', $ctr->Eliminar());
        $router->patch('/usuario/[i:id]/habilitar', $ctr->Habilitar());
        $router->patch('/usuario/[i:id]/deshabilitar', $ctr->Deshabilitar());
        
    }
}
