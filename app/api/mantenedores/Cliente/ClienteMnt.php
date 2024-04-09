<?php
namespace Mnt\mantenedores\Cliente;

use Mnt\mantenedores\Cliente\Http\Routes\ClienteRoutes;

class ClienteMnt
{
    public static function Create($app)
    {
        ClienteRoutes::Routes($app);
    }
}
