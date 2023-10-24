<?php
namespace Mnt\mantenedores\Producto;

use Mnt\mantenedores\Producto\Http\Routes\ProductoRoutes;

class ProductoMnt
{
    public static function Create($app)
    {
        ProductoRoutes::Routes($app);
    }
}
