<?php
namespace Mnt\facturacion\Factura;

use Mnt\facturacion\Factura\Http\Routes\FacturaRoutes;

class FacturaMnt
{
    public static function Create($app)
    {
        FacturaRoutes::Routes($app);
    }
}
