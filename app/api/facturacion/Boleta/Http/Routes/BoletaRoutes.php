<?php
namespace Mnt\facturacion\Boleta\Http\Routes;

use Mnt\facturacion\Boleta\Http\Controller\BoletaController;

class BoletaRoutes
{
    public static function Routes($router)
    {
        $ctr = new BoletaController();
    
        // Rutas
        $router->get('/boleta-buscar', $ctr->Buscar());
        $router->get('/boleta', $ctr->Listar());
        $router->post('/boleta', $ctr->Crear());
        $router->get('/boleta/[i:id]', $ctr->BuscarPorId());
        $router->put('/boleta/[i:id]', $ctr->Actualizar());
        $router->delete('/boleta/[i:id]', $ctr->Eliminar());
        $router->patch('/boleta/[i:id]/habilitar', $ctr->Habilitar());
        $router->patch('/boleta/[i:id]/deshabilitar', $ctr->Deshabilitar());
        $router->get('/boleta-codigo', $ctr->Codigo());
    }
}
