<?php
namespace Mnt\mantenedores\Categoria;

use Mnt\mantenedores\Categoria\Http\Routes\CategoriaRoutes;

class CategoriaMnt
{
    public static function Create($app)
    {
        CategoriaRoutes::Routes($app);
    }
}
