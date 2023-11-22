<?php

namespace Cmd\Services;

use Throwable;
use App\Utils\Auth\AuthApp;
use Mnt\mantenedores\Empleado\EmpleadoMnt;
use Mnt\mantenedores\Usuario\UsuarioMnt;
use Mnt\mantenedores\Empresa\EmpresaMnt;
use Mnt\mantenedores\Sucursal\SucursalMnt;
use Mnt\mantenedores\Producto\ProductoMnt;
use Mnt\mantenedores\Categoria\CategoriaMnt;
use Mnt\facturacion\Facturacion\FacturacionMnt;
use Mnt\facturacion\Factura\FacturaMnt;
use Mnt\facturacion\Boleta\BoletaMnt;
use App\config\Menu\MenuApp;
use App\config\Ubigeo\UbigeoApp;

class Enpoints
{
    /**
     * @endpoints... 
     * @param Throwable
     * validar si hay referencia de clasname y no hay archivo
     * */
    public static function initEndpoints($router)
    {
        AuthApp::Create($router);
        MenuApp::Create($router);
        EmpleadoMnt::Create($router);
        FacturacionMnt::Create($router);
        UsuarioMnt::Create($router);
        EmpresaMnt::Create($router);
        UbigeoApp::Create($router);
        SucursalMnt::Create($router);
        ProductoMnt::Create($router);
        CategoriaMnt::Create($router);
        FacturaMnt::Create($router);
        BoletaMnt::Create($router);
    }
}
