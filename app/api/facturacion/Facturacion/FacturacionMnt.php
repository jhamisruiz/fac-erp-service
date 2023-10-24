<?php
namespace Mnt\facturacion\Facturacion;

use Mnt\facturacion\Facturacion\Http\Routes\FacturacionRoutes;

class FacturacionMnt
{
    public static function Create($app)
    {
        FacturacionRoutes::Routes($app);
    }
}
