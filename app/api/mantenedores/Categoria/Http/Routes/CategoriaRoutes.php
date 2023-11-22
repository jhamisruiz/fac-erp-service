<?php
namespace Mnt\mantenedores\Categoria\Http\Routes;

use Mnt\mantenedores\Categoria\Http\Controller\CategoriaController;

class CategoriaRoutes
{
    public static function Routes($router)
    {
        $ctr = new CategoriaController();
    
        // Rutas
        $router->get('/categoria-buscar', $ctr->Buscar());
        $router->get('/categoria', $ctr->Listar());
        $router->post('/categoria', $ctr->Crear());
        $router->get('/categoria/[i:id]', $ctr->BuscarPorId());
        $router->put('/categoria/[i:id]', $ctr->Actualizar());
        $router->delete('/categoria/[i:id]', $ctr->Eliminar());
        $router->patch('/categoria/[i:id]/habilitar', $ctr->Habilitar());
        $router->patch('/categoria/[i:id]/deshabilitar', $ctr->Deshabilitar());
        $router->get('/categoria-codigo', $ctr->Codigo());
    }
}
