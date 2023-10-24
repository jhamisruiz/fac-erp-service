<?php
namespace App\config\Menu;

use App\config\Menu\Http\Routes\MenuRoutes;

class MenuApp
{
    public static function Create($app)
    {
        MenuRoutes::Routes($app);
    }
}
