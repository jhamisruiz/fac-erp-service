<?php
namespace Mnt\facturacion\Boleta;

use Mnt\facturacion\Boleta\Http\Routes\BoletaRoutes;

class BoletaMnt
{
    public static function Create($app)
    {
        BoletaRoutes::Routes($app);
    }
}
