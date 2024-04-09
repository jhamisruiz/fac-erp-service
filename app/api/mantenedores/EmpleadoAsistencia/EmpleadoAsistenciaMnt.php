<?php
namespace Mnt\mantenedores\EmpleadoAsistencia;

use Mnt\mantenedores\EmpleadoAsistencia\Http\Routes\EmpleadoAsistenciaRoutes;

class EmpleadoAsistenciaMnt
{
    public static function Create($app)
    {
        EmpleadoAsistenciaRoutes::Routes($app);
    }
}
