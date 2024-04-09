<?php

namespace Cmd\Services;

use Throwable;
use App\Auth\AuthApp;
use App\config\Menu\MenuApp;
use App\config\Ubigeo\UbigeoApp;
use Mnt\facturacion\Boleta\BoletaMnt;
use Mnt\facturacion\Factura\FacturaMnt;
use Mnt\mantenedores\Cliente\ClienteMnt;
use Mnt\mantenedores\Empresa\EmpresaMnt;
use Mnt\mantenedores\Usuario\UsuarioMnt;
use Mnt\mantenedores\Empleado\EmpleadoMnt;
use Mnt\mantenedores\Producto\ProductoMnt;
use Mnt\mantenedores\Sucursal\SucursalMnt;
use Mnt\mantenedores\Categoria\CategoriaMnt;
use Mnt\mantenedores\EmpleadoAsistencia\EmpleadoAsistenciaMnt;
use Mnt\mantenedores\Rol\RolMnt;
use Mnt\facturacion\Facturacion\FacturacionMnt;

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
        ClienteMnt::Create($router);
        EmpleadoAsistenciaMnt::Create($router);
        RolMnt::Create($router);
    }
}
