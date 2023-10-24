<?php
namespace Mnt\mantenedores\Usuario;

use Mnt\mantenedores\Usuario\Http\Routes\UsuarioRoutes;

class UsuarioMnt
{
    public static function Create($app)
    {
        UsuarioRoutes::Routes($app);
    }
}
