<?php

namespace Mnt\facturacion\Factura\Http\Routes;

use Mnt\facturacion\Factura\Http\Controller\FacturaController;

class FacturaRoutes
{
    public static function Routes($router)
    {
        $ctr = new FacturaController();

        // Rutas
        $router->get('/factura-buscar', $ctr->Buscar());
        $router->get('/factura', $ctr->Listar());
        $router->post('/factura', $ctr->Crear());
        $router->get('/factura/[i:id]', $ctr->BuscarPorId());
        $router->put('/factura', $ctr->Actualizar()); // /[i:id]
        $router->delete('/factura/[i:id]', $ctr->Eliminar());
        $router->patch('/factura/[i:id]/habilitar', $ctr->Habilitar());
        $router->patch('/factura/[i:id]/deshabilitar', $ctr->Deshabilitar());
        $router->get('/factura-codigo', $ctr->Codigo());
    }
}
