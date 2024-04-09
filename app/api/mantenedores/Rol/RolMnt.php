<?php
namespace Mnt\mantenedores\Rol;

use Mnt\mantenedores\Rol\Http\Routes\RolRoutes;

class RolMnt
{
    public static function Create($app)
    {
        RolRoutes::Routes($app);
    }
}
