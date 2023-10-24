<?php
namespace App\config\Ubigeo;

use App\config\Ubigeo\Http\Routes\UbigeoRoutes;

class UbigeoApp
{
    public static function Create($app)
    {
        UbigeoRoutes::Routes($app);
    }
}
