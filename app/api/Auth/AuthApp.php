<?php

namespace App\Auth;

use App\Auth\Http\Routes\AuthRoutes;

class AuthApp
{
    public static function Create($app)
    {

        AuthRoutes::Routes($app);
    }
}
