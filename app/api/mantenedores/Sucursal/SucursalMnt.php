<?php
namespace Mnt\mantenedores\Sucursal;

use Mnt\mantenedores\Sucursal\Http\Routes\SucursalRoutes;

class SucursalMnt
{
    public static function Create($app)
    {
        SucursalRoutes::Routes($app);
    }
}
