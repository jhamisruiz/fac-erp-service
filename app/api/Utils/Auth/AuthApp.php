<?php

namespace App\Utils\Auth;

use App\Utils\Auth\Http\Routes\AuthRoutes;

class AuthApp
{
    public static function Create($app)
    {

        AuthRoutes::Routes($app);
    }
}
