<?php
namespace Mnt\mantenedores\Empleado;

use Mnt\mantenedores\Empleado\Http\Routes\EmpleadoRoutes;

class EmpleadoMnt
{
    public static function Create($app)
    {
        EmpleadoRoutes::Routes($app);
    }
}
