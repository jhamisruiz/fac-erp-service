<?php

namespace Mnt\facturacion\Facturacion\Http\Routes;

use Mnt\facturacion\Facturacion\Http\Controller\FacturacionController;

class FacturacionRoutes
{
    public static function Routes($router)
    {
        $ctr = new FacturacionController();

        // Rutas
        $router->post('/facturacion-factura', $ctr->Factura());
        $router->post('/facturacion-boleta', $ctr->Boleta());
        $router->post('/facturacion-nota-credito', $ctr->NotaCredito());
        $router->post('/facturacion-nota-debito', $ctr->NotaDebito());
        $router->post('/facturacion-gia-remision', $ctr->GiraRemision());
        $router->post('/facturacion-baja-suntat', $ctr->BajaSuna());
        $router->post('/facturacion-resumen-boletas', $ctr->ResumenBoletas());
        //lta documentos
        $router->get('/facturacion-documento-buscar', $ctr->Buscar());
    }
}
