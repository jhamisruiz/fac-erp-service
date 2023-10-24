<?php
namespace Mnt\mantenedores\Empresa;

use Mnt\mantenedores\Empresa\Http\Routes\EmpresaRoutes;

class EmpresaMnt
{
    public static function Create($app)
    {
        EmpresaRoutes::Routes($app);
    }
}
