<?php

namespace Mnt\mantenedores\Producto\Http\Routes;

use Mnt\mantenedores\Producto\Http\Controller\ProductoController;

class ProductoRoutes
{
    public static function Routes($router)
    {
        $ctr = new ProductoController();

        // Rutas
        $router->get('/producto-buscar', $ctr->Buscar());
        $router->get('/producto', $ctr->Listar());
        $router->post('/producto', $ctr->Crear());
        $router->get('/producto/[i:id]', $ctr->BuscarPorId());
        $router->put('/producto/[i:id]', $ctr->Actualizar());
        $router->delete('/producto/[i:id]', $ctr->Eliminar());
        $router->patch('/producto/[i:id]/habilitar', $ctr->Habilitar());
        $router->patch('/producto/[i:id]/deshabilitar', $ctr->Deshabilitar());
        $router->get('/producto-codigo', $ctr->Codigo());
        // Rutas unp
        $router->get('/producto-unspsc-segmentos', $ctr->Segmentos());
        $router->get('/producto-unspsc-familias/[i:codigo]', $ctr->Familias());
        $router->get('/producto-unspsc-clases/[i:codigo]', $ctr->Clases());
        $router->get('/producto-unspsc-productos', $ctr->Productos());
    }
}
