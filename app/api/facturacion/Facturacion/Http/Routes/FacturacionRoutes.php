<?php

namespace Mnt\facturacion\Facturacion\Http\Routes;

use Mnt\facturacion\Facturacion\Http\Controller\FacturacionController;

class FacturacionRoutes
{
    public static function Routes($router)
    {
        $ctr = new FacturacionController();

        // Rutas
        $router->post('/documento-factura', $ctr->Factura());
        $router->post('/documento-boleta', $ctr->Boleta());
        $router->post('/documento-nota-credito', $ctr->NotaCredito());
        $router->post('/documento-nota-debito', $ctr->NotaDebito());
        $router->post('/documento-gia-remision', $ctr->GiraRemision());
        $router->post('/documento-baja-suntat', $ctr->BajaSuna());
        $router->post('/documento-resumen-boletas', $ctr->ResumenBoletas());
        //lta documentos
        $router->get('/documento-tipo-buscar', $ctr->Buscar());
        $router->get('/tipo-afectacion-buscar', $ctr->AfectacionBuscar());
    }
}
